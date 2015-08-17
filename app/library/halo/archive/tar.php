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
 
class HALOArchiveTar extends HALOObject
{
	/**
	 * Tar file types.
	 * @var array
	 */
	var $_types = array (
		0x0 => 'Unix file',
		0x30 => 'File',
		0x31 => 'Link',
		0x32 => 'Symbolic link',
		0x33 => 'Character special file',
		0x34 => 'Block special file',
		0x35 => 'Directory',
		0x36 => 'FIFO special file',
		0x37 => 'Contiguous file'
	);

	/**
	 * Tar file flags.
	 * @var array
	 */
	var $_flags = array (
		'FTEXT' => 0x01,
		'FHCRC' => 0x02,
		'FEXTRA' => 0x04,
		'FNAME' => 0x08,
		'FCOMMENT' => 0x10
	);

	/**
	 * Tar file data buffer
	 * @var string
	 */
	var $_data = null;

	/**
	 * Tar file metadata array
	 * @var array
	 */
	var $_metadata = null;

	/**
	* Extract a ZIP compressed file to a given path
	*
	* @access	public
	* @param	string	$archive		Path to ZIP archive to extract
	* @param	string	$destination	Path to extract archive into
	* @param	array	$options		Extraction options [unused]
	* @return	boolean	True if successful
	*/
	function extract($archive, $destination, $options = array ())
	{
		// Initialize variables
		$this->_data = null;
		$this->_metadata = null;

		if (!$this->_data = File::get($archive))
		{
			$this->set('error.message', 'Unable to read archive');
			return false;
		}

		if (!$this->_getTarInfo($this->_data))
		{
			return false;
		}

		for ($i=0,$n=count($this->_metadata);$i<$n;$i++)
		{
			$type	= strtolower( $this->_metadata[$i]['type'] );
			if ($type == 'file' || $type == 'unix file')
			{
				$buffer = $this->_metadata[$i]['data'];
				$path = File::cleanPath($destination.'/'.$this->_metadata[$i]['name']);
				// Make sure the destination folder exists
				if (!File::createDir(dirname($path)))
				{
					$this->set('error.message', 'Unable to create destination');
					return false;
				}
				if (File::writeFile($path, $buffer) === false)
				{
					$this->set('error.message', 'Unable to write entry');
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Get the list of files/data from a Tar archive buffer.
	 *
	 * @access	private
	 * @param 	string	$data	The Tar archive buffer.
	 * @return	array	Archive metadata array
	 * <pre>
	 * KEY: Position in the array
	 * VALUES: 'attr'  --  File attributes
	 *         'data'  --  Raw file contents
	 *         'date'  --  File modification time
	 *         'name'  --  Filename
	 *         'size'  --  Original file size
	 *         'type'  --  File type
	 * </pre>
	 */
	function _getTarInfo(& $data)
	{
		$position = 0;
		$return_array = array ();

		while ($position < strlen($data))
		{
			$info = @ unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/Ctypeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor", substr($data, $position));
			if (!$info) {
				$this->set('error.message', 'Unable to decompress data');
				return false;
			}

			$position += 512;
			$contents = substr($data, $position, octdec($info['size']));
			$position += ceil(octdec($info['size']) / 512) * 512;

			if ($info['filename']) {
				$file = array (
					'attr' => null,
					'data' => null,
					'date' => octdec($info['mtime']
				), 'name' => trim($info['filename']), 'size' => octdec($info['size']), 'type' => isset ($this->_types[$info['typeflag']]) ? $this->_types[$info['typeflag']] : null);

				if (($info['typeflag'] == 0) || ($info['typeflag'] == 0x30) || ($info['typeflag'] == 0x35)) {
					/* File or folder. */
					$file['data'] = $contents;

					$mode = hexdec(substr($info['mode'], 4, 3));
					$file['attr'] = (($info['typeflag'] == 0x35) ? 'd' : '-') .
					 (($mode & 0x400) ? 'r' : '-') .
					 (($mode & 0x200) ? 'w' : '-') .
					 (($mode & 0x100) ? 'x' : '-') .
					 (($mode & 0x040) ? 'r' : '-') .
					 (($mode & 0x020) ? 'w' : '-') .
					 (($mode & 0x010) ? 'x' : '-') .
					 (($mode & 0x004) ? 'r' : '-') .
					 (($mode & 0x002) ? 'w' : '-') .
					 (($mode & 0x001) ? 'x' : '-');
				} else {
					/* Some other type. */
				}
				$return_array[] = $file;
			}
		}
		$this->_metadata = $return_array;
		return true;
	}
}
