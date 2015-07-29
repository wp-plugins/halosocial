<?php
/*
 * Plugin Name: HaloSocial
 * Plugin URL: https://halo.social
 * Description: Social Networking Plugin for WordPress
 * Author: HaloSocial
 * Author URL: https://halo.social
 * Version: 1.0
 * Copyright: (c) 2015 HaloSocial, Inc. All Rights Reserved.
 * License: GPLv3 or later
 * License URL: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: halosocial
 * Domain Path: /language
 *
 * HaloSocial is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * HaloSocial is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY. See the
 * GNU General Public License for more details.
 */

class MediaController extends BaseController
{

    /**
     * Inject the models.
     * @param HALOFieldModel $field
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * process ajax Upload file
     *
     * @return JSON
     */

    public function ajaxUpload()
    {
        //1.cookie checking is required to determind the media owner

        //2. check the file extension is allowed
        //check mediaType
        if (!isset($_REQUEST["mediaType"])) {
            $this->responseError('', 101, __halotext('Invalid media type.'));
        }

        //3. check if user has permission to upload files
        //3.1 check quota
        if (lcfirst($_REQUEST["mediaType"]) == 'photo') {
            if (HALOQuotaAPI::exceed('photo.create', new HALOPhotoModel(), 'owner_id')) {
                $mb = HALOResponse::getMessage();
                $this->responseError('', 101, $mb->first('error'));
            }
        }
        if (lcfirst($_REQUEST["mediaType"]) == 'file') {
            if (HALOQuotaAPI::exceed('file.create', new HALOFileModel(), 'owner_id')) {
                $mb = HALOResponse::getMessage();
                $this->responseError('', 101, $mb->first('error'));
            }
        }

        //4. proceed to store the uploading file to tmp file
        $uploadFile = $this->handleUpload();

        //verify file size
        if (!HALOUtilHelper::verifyFileSize($uploadFile)) {
            $this->responseError($uploadFile, 101, __halotext('File size exceeds permissible limit'));
        }

        $mediaType = $_REQUEST["mediaType"];

        $processFunc = 'process' . ucfirst($mediaType);
        if (method_exists($this, $processFunc)) {
            call_user_func_array(array($this, $processFunc), array($uploadFile));
        } else {
            $this->responseError($uploadFile, 101, __halotext('Invalid media type.'));
        }
    }

    /**
     * response error message for file upload
     *
     * @param  string $file
     * @param  string $code
     * @param  string $message
     */
    protected function responseError($file, $code, $message)
    {
        if ($file && file_exists($file)) {
            File::delete($file);
        }
        die('{"jsonrpc" : "2.0", "error" : {"code": ' . $code . ', "message": "' . $message . '"}, "id" : "id"}');
        return;
    }
    /**
     * process upload Photo
     *
     * @param  string $uploadFile
     * @return JSON
     */
    protected function processPhoto($uploadFile)
    {
        //5. move the uploaded tmp file from tmp folder to configured folder with hash file name
        $media = new HALOPhotoModel();
        //verify file type
        if (!$media->verifyFileType($uploadFile)) {
            $this->responseError($uploadFile, 101, __halotext('File Extension is not allowed'));
        }
        //verify photo size
        if (!$media->verifyFileSize($uploadFile)) {
            $this->responseError($uploadFile, 101, __halotext('Maximum photo dimension is 2500 x 1920'));
        }
        if (!$media->copyFileFrom($uploadFile)) {
            $this->responseError($uploadFile, 101, __halotext('Failed to move file to storage'));
        }
        //trigger on before adding file
        if (Event::fire('photo.onBeforeAdding', array($uploadFile), true) === false) {
            //error occur, return
            $this->responseError($uploadFile, 101, __halotext('Failed to move file to storage'));
        }
        //6. store the file to db
        try {
            $media->storage = 'file';
            $media->save();
        } catch (Exception $e) {
            $this->responseError($uploadFile, 101, __halotext('Failed to store to database'));
        }

        //trigger on after adding file
        Event::fire('file.onAfterAdding', array($media));

        //7. delete the tmp file
        File::delete($uploadFile);

        //8. return to client with the media_id, photo thumnail
        $photoWidth = HALO_PHOTO_THUMB_SIZE;
        if (isset($_REQUEST["photowidth"])) {
            $photoWidth = intval($_REQUEST["photowidth"]);
        }
        if (isset($_REQUEST["photoheight"]) && !empty($_REQUEST["photoheight"])) {
            $photoHeight = intval($_REQUEST["photoheight"]);
        } else {
            //default is square image
            $photoHeight = $photoWidth;
        }

        //correct photo orientation
        $media->orientate();

        if ($photoHeight == 0 && $photoWidth == 0) {
            //there is no height & width configured, just return full size photo
            $thumbnail = $media->getPhotoURL();
        } else {
						//make the photo have a better look
            $thumbnail = $media->getResizePhotoURL('center', $photoHeight * 4);

        }

        // Return Success JSON-RPC response
        $jsonrpc = HALOObject::getInstance(array('jsonrpc' => '2.0', 'result' => null, 'id' => $media->id, 'image' => $thumbnail));
        die(json_encode($jsonrpc));

    }
    /**
     * process upload Video
     *
     * @param  string $uploadFile
     */
    protected function processVideo($uploadFile)
    {

    }
    /**
     * process upload File
     * @param  string $uploadFile
     * @return JSON
     */
    protected function processFile($uploadFile)
    {
        //5. move the uploaded tmp file from tmp folder to configured folder with hash file name
        $media = new HALOFileModel();
        if (!$media->verifyFileType($uploadFile)) {
            $this->responseError($uploadFile, 101, __halotext('File Extension is not allowed'));
        }
        if (!$media->copyFileFrom($uploadFile)) {
            $this->responseError($uploadFile, 101, __halotext('Failed to move file to storage'));
        }

        //trigger on before adding file
        if (Event::fire('file.onBeforeAdding', array($uploadFile), true) === false) {
            //error occur, return
            $this->responseError($uploadFile, 101, __halotext('Failed to move file to storage'));
        }
        //6. store the file to db
        try {
            $media->save();
        } catch (Exception $e) {
            $this->responseError($uploadFile, 101, __halotext('Failed to store to database'));
        }

        //trigger on after adding file
        Event::fire('file.onAfterAdding', array($media));

        //7. delete the tmp file
        File::delete($uploadFile);

        //8. return to client with the media_id, photo thumnail
        $photoWidth = HALO_PHOTO_THUMB_SIZE;
        if (isset($_REQUEST["photowidth"])) {
            $photoWidth = intval($_REQUEST["photowidth"]);
        }
        if (isset($_REQUEST["photoheight"]) && !empty($_REQUEST["photoheight"])) {
            $photoHeight = intval($_REQUEST["photoheight"]);
        } else {
            //default is square image
            $photoHeight = $photoWidth;
        }
        $thumbnail = $media->getThumbnail($photoWidth, $photoHeight);

        // Return Success JSON-RPC response
        $jsonrpc = HALOObject::getInstance(array('jsonrpc' => '2.0', 'result' => null, 'id' => $media->id, 'image' => $thumbnail));
        die(json_encode($jsonrpc));

    }
    /**
     * process upload Audio
     *
     * @param  string $uploadFile
     */
    protected function processAudio($uploadFile)
    {

    }

    /**
     * Read the upload file from global vars and put to tmp folder. Join files if chunk is set.
     * Return the uploaded file if success, (can halt the process on error for quickly response.)
     *
     */
    protected function handleUpload()
    {
        // $tmpDir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
        // $targetDir = $tmpDir . DIRECTORY_SEPARATOR . "halosocial";

        // Create target dir
        // if (!file_exists($targetDir)) {
            // @mkdir($targetDir);
        // }

		$targetDir = HALOAssetHelper::getUploadTmpDir();
        //file uploading framework
        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["async-upload"]["name"];
        } else {
            $fileName = uniqid("file_");
        }

        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        // Remove old temp files
        $cleanupTargetDir = true;
        $maxFileAge = 5 * 3600;// Temp file age in seconds

        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                $this->responseError('', 100, __halotext('Failed to open temp directory'));
            }

            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}.part") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

        // Open temp file
        if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
            $this->responseError('', 102, __halotext('Failed to open output stream'));
        }

        if (!empty($_FILES)) {
            if ($_FILES["async-upload"]["error"] || !is_uploaded_file($_FILES["async-upload"]["tmp_name"])) {
                $this->responseError('', 103, __halotext('Failed to move uploaded file'));
            }

            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["async-upload"]["tmp_name"], "rb")) {
                $this->responseError('', 101, __halotext('Failed to open input stream'));
            }
        } else {

            if (!$in = @fopen("php://input", "rb")) {
                $this->responseError('', 101, __halotext('Failed to open input stream'));
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$filePath}.part", $filePath);
            //if the file is uploaded we store it into db with a hashname
            return $filePath;
        } else {
            //still some chunks waiting, just release
            // Return Success JSON-RPC response
            die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

        }

    }

}
