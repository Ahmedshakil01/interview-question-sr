@if (count($messages))
<div class="row">
  <div class="col-md-12">
  @foreach ($messages as $message)
      <div class="alert alert-{{ $message['level'] }} customcss">{!! $message['message'] !!}</div>
  @endforeach
  </div>
</div>
@endif

<style>
	.customcss{
		padding:20px;	
	}
</style>