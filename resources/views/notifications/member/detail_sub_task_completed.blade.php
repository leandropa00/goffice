<div class="media">
    <div class="media-body">
        <h5 class="media-heading"><span class="btn btn-circle btn-success"><i class="fa fa-tasks"></i></span>{{ ucfirst($notification->data['heading']) }} - @lang('email.subTaskComplete.subject')</h5>
        </div>
    <h6><i>@if($notification->data['completed_on']){{ \Carbon\Carbon::parse( $notification->data['completed_on'])->diffForHumans() }} @endif</i></h6>
</div>
