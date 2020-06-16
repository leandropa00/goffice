<div class="media">
    <div class="media-body">
        <h5 class="media-heading"><span class="btn btn-circle btn-inverse"><i class="icon-doc"></i></span> {{ __('email.invoice.subject') . ' - ' . $notification->data['invoice_number']  }} @if(isset($notification->data['project'])) {{ ucwords($notification->data['project']['project_name']) }}@endif</h5>
    </div>
    <h6><i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</i></h6>
</div>
