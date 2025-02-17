<div class="page-header min-vh-75 d-flex align-items-center justify-content-center" style="background: url('{{asset('/img/header-jobs-update.svg')}}')">
    <div class="header-gradient-wrapper  d-flex w-100">

        <div class="container py-5">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-12 col-md-8 py-4">

                    <div class="d-flex justify-content-center align-items-center mb-5">
                        <div>
                            <h2 class="font-weight-bolder text-center">{{__('create_page_header')}}</h2>
                            <div class="d-flex justify-content-center">
                                <div class="w-75">
                                    <p class="text-center mb-0">{{__('create_page_subheader')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <div class="row mx-1 mx-md-0">

                            <div class="col-3 p-0">
                                <div class="text-primary font-weight-bold">{{__('Step :number', ['number' => 1])}} </div>
                                <div class="d-flex">
                                    <div class="col-8 p-0">
                                        <div class="{{ in_array($step, [1,2,3,4]) ? 'bg-primary' : 'bg-light' }} my-2 step-indicator-line"></div>
                                    </div>
                                    <div class="col-4 p-0">
                                        <div class="{{in_array($step, [2,3,4]) ? 'bg-primary' : 'bg-light'}} my-2 step-indicator-line"></div>
                                    </div>
                                </div>

                                <div class="font-weight-bolder h6 d-md-none">{{__('Create')}}</div>
                                <div class="font-weight-bolder h5 d-none d-md-block">{{__('Create')}}</div>
                            </div>
                            <div class="col-3 p-0">
                                <div class="text-primary font-weight-bold">{{__('Step :number', ['number' => 2])}} </div>
                                <div class="d-flex">
                                    <div class="col-8 p-0">
                                        <div class="{{ in_array($step, [2,3,4]) ? 'bg-primary' : 'bg-light' }} my-2 step-indicator-line"></div>
                                    </div>
                                    <div class="col-4 p-0">
                                        <div class="{{in_array($step, [3,4]) ? 'bg-primary' : 'bg-light'}} my-2 step-indicator-line"></div>
                                    </div>
                                </div>

                                <div class="font-weight-bolder h6 d-md-none">{{__('Preview')}}</div>
                                <div class="font-weight-bolder h5 d-none d-md-block">{{__('Preview')}}</div>
                            </div>
                            <div class="col-3 p-0">
                                <div class="text-primary font-weight-bold">{{__('Step :number', ['number' => 3])}} </div>
                                <div class="d-flex">
                                    <div class="col-8 p-0">
                                        <div class="{{ in_array($step, [3,4]) ? 'bg-primary' : 'bg-light' }} my-2 step-indicator-line"></div>
                                    </div>
                                    <div class="col-4 p-0">
                                        <div class="{{in_array($step, [4]) ? 'bg-primary' : 'bg-light'}} my-2 step-indicator-line"></div>
                                    </div>
                                </div>
                                <div class="font-weight-bolder h6 d-md-none">{{__('Package')}}</div>
                                <div class="font-weight-bolder h5 d-none d-md-block">{{__('Package')}}</div>
                            </div>
                            <div class="col-3 p-0">
                                <div class="text-primary font-weight-bold">{{__('Step :number', ['number' => 4])}} </div>
                                <div class="d-flex">
                                    <div class="col-8 p-0">
                                        <div class="{{ in_array($step, [4]) ? 'bg-primary' : 'bg-light' }} my-2 step-indicator-line"></div>
                                    </div>
                                    <div class="col-4 p-0">
                                        <div class="{{in_array($step, []) ? 'bg-primary' : 'bg-light'}} my-2 step-indicator-line"></div>
                                    </div>
                                </div>
                                <div class="font-weight-bolder h6 d-md-none">{{__('Purchase')}}</div>
                                <div class="font-weight-bolder h5 d-none d-md-block">{{__('Purchase')}}</div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
