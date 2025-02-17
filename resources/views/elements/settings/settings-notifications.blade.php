<div class="mt-3 mt-md-0">

    <form>
        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input notification-checkbox" id="notification_email_expiring_subs" name="notification_email_expiring_subs"
                    {{isset(Auth::user()->settings['notification_email_expiring_subs']) ? (Auth::user()->settings['notification_email_expiring_subs'] == 'true' ? 'checked' : '') : false}}>
                <label class="custom-control-label" for="notification_email_expiring_subs">{{__('Expiring subscriptions')}}</label>
            </div>
        </div>
        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input notification-checkbox" id="notification_email_renewals" name="notification_email_renewals"
                    {{isset(Auth::user()->settings['notification_email_renewals']) ? (Auth::user()->settings['notification_email_renewals'] == 'true' ? 'checked' : '') : false}}>
                <label class="custom-control-label" for="notification_email_renewals">{{__('Upcoming renewals')}}</label>
            </div>
        </div>
    </form>
</div>
