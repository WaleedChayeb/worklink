<div class="modal fade " tabindex="-1" role="dialog" id="login-dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content p-2">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="block-user-label">{{__('Login or register')}}</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-lg-12 d-flex align-items-center pl">
                        @include('auth.modal-forms')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
