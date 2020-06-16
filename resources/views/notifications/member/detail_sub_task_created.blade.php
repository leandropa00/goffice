<div class="media">
    <div class="media-body">
        <h5 class="media-heading"><span class="btn btn-circle btn-success"><i class="fa fa-tasks"></i></span>{{ ucfirst($notification->data['heading']) }} - @lang('email.subTaskCreated')</h5>
        </div>
    <h6><i>@if($notification->created_at){{ \Carbon\Carbon::parse( $notification->created_at)->diffForHumans() }} @endif</i></h6>
</div>
