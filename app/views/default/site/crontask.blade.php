@if(empty($section))
<h4> {{__halotext('Running System Crontask')}}</h4>
@else 
<h4> {{__halotext('Running Crontask for:') . $section}}</h4>
@endif
@foreach($messages as $message)
	{{$message}} <br>
@endforeach