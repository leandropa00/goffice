<li class="top-notifications">
    <div class="message-center">
        <a href="javascript:;" class="show-all-notifications">
            <div class="user-img">
                <span class="btn btn-circle btn-success"><i class="fa fa-tasks"></i></span>
            </div>
            <div class="mail-contnet">
                <span class="mail-desc m-0">{{ ucfirst($notification->data['heading']) }} - @lang('email.taskUpdate.subject')!</span> <span class="time">{{ \Carbon\Carbon::parse( $notification->data['updated_at'])->diffForHumans() }}</span>
            </div>
        </a>
    </div>
</li>
