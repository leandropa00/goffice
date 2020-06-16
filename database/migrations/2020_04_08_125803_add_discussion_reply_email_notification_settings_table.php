<?php

use App\EmailNotificationSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscussionReplyEmailNotificationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        EmailNotificationSetting::create(
            [
                'setting_name' => 'Discussion Reply',
                'send_email' => 'yes',
                'slug' => 'discussion-reply'
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        EmailNotificationSetting::where('slug', 'discussion-reply')->delete();
    }
}
