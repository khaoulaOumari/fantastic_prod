@if($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- Name Field -->
    <div class="form-group row ">
        {!! Form::label('name', trans("lang.restaurant_name"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.restaurant_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_name_help") }}
            </div>
        </div>
    </div>
    <!-- cuisines Field -->
    <!-- <div class="form-group row ">
        {!! Form::label('cuisines[]', trans("lang.restaurant_cuisines"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('cuisines[]', $cuisine, $cuisinesSelected, ['class' => 'select2 form-control' , 'multiple'=>'multiple']) !!}
            <div class="form-text text-muted">{{ trans("lang.restaurant_cuisines_help") }}</div>
        </div>
    </div> -->
    @hasanyrole('admin|manager')
    <!-- Users Field -->
    <div class="form-group row ">
        {!! Form::label('drivers[]', trans("lang.restaurant_drivers"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('drivers[]', $drivers, $driversSelected, ['class' => 'select2 form-control' , 'multiple'=>'multiple']) !!}
            <div class="form-text text-muted">{{ trans("lang.restaurant_drivers_help") }}</div>
        </div>
    </div>
    <!-- delivery_fee Field -->
    <div class="form-group row ">
        {!! Form::label('delivery_fee', trans("lang.restaurant_delivery_fee"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('delivery_fee', null,  ['class' => 'form-control','step'=>'any','placeholder'=>  trans("lang.restaurant_delivery_fee_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_delivery_fee_help") }}
            </div>
        </div>
    </div>

    <!-- delivery_range Field -->
    <!-- <div class="form-group row ">
        {!! Form::label('delivery_range', trans("lang.restaurant_delivery_range"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('delivery_range', null,  ['class' => 'form-control', 'step'=>'any','placeholder'=>  trans("lang.restaurant_delivery_range_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_delivery_range_help") }}
            </div>
        </div>
    </div> -->

    <!-- default_tax Field -->
    <!-- <div class="form-group row ">
        {!! Form::label('default_tax', trans("lang.restaurant_default_tax"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('default_tax', null,  ['class' => 'form-control', 'step'=>'any','placeholder'=>  trans("lang.restaurant_default_tax_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_default_tax_help") }}
            </div>
        </div>
    </div> -->

    @endhasanyrole

    <!-- Phone Field -->
    <div class="form-group row ">
        {!! Form::label('phone', trans("lang.restaurant_phone"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('phone', null,  ['class' => 'form-control','placeholder'=>  trans("lang.restaurant_phone_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_phone_help") }}
            </div>
        </div>
    </div>

    <!-- Mobile Field -->
    <div class="form-group row ">
        {!! Form::label('mobile', trans("lang.restaurant_mobile"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('mobile', null,  ['class' => 'form-control','placeholder'=>  trans("lang.restaurant_mobile_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_mobile_help") }}
            </div>
        </div>
    </div>

    <!-- Address Field -->
    

    <!-- Latitude Field -->
    <!-- <div class="form-group row ">
        {!! Form::label('latitude', trans("lang.restaurant_latitude"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('latitude', null,  ['class' => 'form-control','placeholder'=>  trans("lang.restaurant_latitude_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_latitude_help") }}
            </div>
        </div>
    </div> -->

    <!-- Longitude Field -->
    <!-- <div class="form-group row ">
        {!! Form::label('longitude', trans("lang.restaurant_longitude"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('longitude', null,  ['class' => 'form-control','placeholder'=>  trans("lang.restaurant_longitude_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_longitude_help") }}
            </div>
        </div>
    </div> -->
    <!-- 'Boolean closed Field' -->
    <!-- <div class="form-group row ">
        {!! Form::label('closed', trans("lang.restaurant_closed"),['class' => 'col-3 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('closed', 0) !!}
                {!! Form::checkbox('closed', 1, null) !!}
            </label>
        </div>
    </div> -->

    

    

    <!-- <div class="form-group row ">
        {!! Form::label('closed', trans("lang.restaurant_closed"),['class' => 'col-3 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('closed', 0) !!}
                {!! Form::checkbox('closed', 1, null) !!}
            </label>
        </div>
    </div> -->

    <!-- 'Boolean available_for_delivery Field' -->
    <div class="form-group row ">
        {!! Form::label('available_for_delivery', trans("lang.restaurant_available_for_delivery"),['class' => 'col-3 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('available_for_delivery', 0) !!}
                {!! Form::checkbox('available_for_delivery', 1, null) !!}
            </label>
        </div>
    </div>

</div>
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

    <!-- Image Field -->
    <div class="form-group row">
        {!! Form::label('image', trans("lang.restaurant_image"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            <div style="width: 100%" class="dropzone image" id="image" data-field="image">
                <input type="hidden" name="image">
            </div>
            <!-- <a href="#loadMediaModal" data-dropzone="image" data-toggle="modal" data-target="#mediaModal" class="btn btn-outline-{{setting('theme_color','primary')}} btn-sm float-right mt-1">{{ trans('lang.media_select')}}</a> -->
            <div class="form-text text-muted w-50">
                {{ trans("lang.restaurant_image_help") }}
            </div>
        </div>
    </div>
    @prepend('scripts')
        <script type="text/javascript">
            var var15671147011688676454ble = '';
            @if(isset($restaurant) && $restaurant->hasMedia('image'))
                var15671147011688676454ble = {
                name: "{!! $restaurant->getFirstMedia('image')->name !!}",
                size: "{!! $restaurant->getFirstMedia('image')->size !!}",
                type: "{!! $restaurant->getFirstMedia('image')->mime_type !!}",
                collection_name: "{!! $restaurant->getFirstMedia('image')->collection_name !!}"
            };
                    @endif
            var dz_var15671147011688676454ble = $(".dropzone.image").dropzone({
                    url: "{!!url('uploads/store')!!}",
                    addRemoveLinks: true,
                    maxFiles: 1,
                    init: function () {
                        @if(isset($restaurant) && $restaurant->hasMedia('image'))
                        dzInit(this, var15671147011688676454ble, '{!! url($restaurant->getFirstMediaUrl('image','thumb')) !!}')
                        @endif
                    },
                    accept: function (file, done) {
                        dzAccept(file, done, this.element, "{!!config('medialibrary.icons_folder')!!}");
                    },
                    sending: function (file, xhr, formData) {
                        dzSending(this, file, formData, '{!! csrf_token() !!}');
                    },
                    maxfilesexceeded: function (file) {
                        dz_var15671147011688676454ble[0].mockFile = '';
                        dzMaxfile(this, file);
                    },
                    complete: function (file) {
                        dzComplete(this, file, var15671147011688676454ble, dz_var15671147011688676454ble[0].mockFile);
                        dz_var15671147011688676454ble[0].mockFile = file;
                    },
                    removedfile: function (file) {
                        dzRemoveFile(
                            file, var15671147011688676454ble, '{!! url("restaurants/remove-media") !!}',
                            'image', '{!! isset($restaurant) ? $restaurant->id : 0 !!}', '{!! url("uplaods/clear") !!}', '{!! csrf_token() !!}'
                        );
                    }
                });
            dz_var15671147011688676454ble[0].mockFile = var15671147011688676454ble;
            dropzoneFields['image'] = dz_var15671147011688676454ble;
        </script>
@endprepend

<!-- Description Field -->
    <div class="form-group row ">
        {!! Form::label('description', trans("lang.restaurant_description"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::textarea('description', null, ['class' => 'form-control','placeholder'=>
             trans("lang.restaurant_description_placeholder")  ]) !!}
            <div class="form-text text-muted">{{ trans("lang.restaurant_description_help") }}</div>
        </div>
    </div>
    <!-- Information Field -->
    <!-- <div class="form-group row ">
        {!! Form::label('information', trans("lang.restaurant_information"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::textarea('information', null, ['class' => 'form-control','placeholder'=>
             trans("lang.restaurant_information_placeholder")  ]) !!}
            <div class="form-text text-muted">{{ trans("lang.restaurant_information_help") }}</div>
        </div>
    </div> -->

</div>

@hasrole('admin')
<div class="col-12 custom-field-container">
    <h5 class="col-12 pb-4">{!! trans('lang.admin_area') !!}</h5>
    <div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
        <!-- Users Field -->
        <div class="form-group row ">
            {!! Form::label('users[]', trans("lang.restaurant_users"),['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
                {!! Form::select('users[]', $user, $usersSelected, ['class' => 'select2 form-control' , 'multiple'=>'multiple']) !!}
                <div class="form-text text-muted">{{ trans("lang.restaurant_users_help") }}</div>
            </div>
        </div>

    </div>
    <!-- <div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
         admin_commission Field 
        <div class="form-group row ">
            {!! Form::label('admin_commission', trans("lang.restaurant_admin_commission"), ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
                {!! Form::number('admin_commission', null,  ['class' => 'form-control', 'step'=>'any', 'placeholder'=>  trans("lang.restaurant_admin_commission_placeholder")]) !!}
                <div class="form-text text-muted">
                    {{ trans("lang.restaurant_admin_commission_help") }}
                </div>
            </div>
        </div>
        <div class="form-group row ">
            {!! Form::label('active', trans("lang.restaurant_active"),['class' => 'col-3 control-label text-right']) !!}
            <div class="checkbox icheck">
                <label class="col-9 ml-2 form-check-inline">
                    {!! Form::hidden('active', 0) !!}
                    {!! Form::checkbox('active', 1, null) !!}
                </label>
            </div>
        </div>
    </div> -->
</div>
@endhasrole


<div class="col-12 custom-field-container">
            
        <h5 class="col-12 pb-4">{!! trans('lang.restaurant_horaire') !!}</h5>
                            
        <div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
            <!-- Users Field -->
            <div class="form-group row ">
                {!! Form::label('start_date', trans("lang.restaurant_start_date"),['class' => 'col-3 control-label text-right']) !!}
                <div class="col-9">
                    {!! Form::time('start_date', null,  ['class' => 'form-control','autocomplete'=>'off'  ]) !!}
                    <div class="form-text text-muted">
                    <!-- {{ trans("lang.coupon_expires_at_help") }} -->
                    </div>
                </div>
            </div>

        </div>
        <div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
            <div class="form-group row ">
                {!! Form::label('end_date', trans("lang.restaurant_end_date"), ['class' => 'col-3 control-label text-right']) !!}
                <div class="col-9">
                    {!! Form::time('end_date', null,  ['class' => 'form-control','autocomplete'=>'off'  ]) !!}
                    <div class="form-text text-muted">
                    <!-- {{ trans("lang.coupon_expires_at_help") }} -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 custom-field-container">
        <h5 class="col-12 pb-4">Adresse du supèrette</h5>
            <div class="col-12">
                <div class="form-group">
                    {!! Form::label('address', trans("lang.restaurant_address"), ['class' => ' control-label text-right']) !!}
                        {!! Form::text('address', null,  ['class' => 'form-control','placeholder'=>  trans("lang.restaurant_address_placeholder"), 'id' => 'address']) !!}
                        <!-- <div class="form-text text-muted">
                            {{ trans("lang.restaurant_address_help") }}
                        </div> -->
                </div>
            </div>

            <div class="col-6">

            <div class="form-group">
            {!! Form::label('latitude', trans("lang.restaurant_latitude"), ['class' => 'control-label text-right']) !!}
                {!! Form::text('latitude', null,  ['class' => 'form-control','placeholder'=>  trans("lang.restaurant_latitude_placeholder"), 'id' => 'updatedlatitude']) !!}
                
            </div>
        </div>

        <!-- Longitude Field -->
        <div class="col-6">
            <div class="form-group">
            {!! Form::label('longitude', trans("lang.restaurant_longitude"), ['class' => 'control-label text-right']) !!}
                {!! Form::text('longitude', null,  ['class' => 'form-control','placeholder'=>  trans("lang.restaurant_longitude_placeholder"), 'id' => 'updatedlongitude']) !!}
                
            </div>
        </div>

                <!-- <div class="col-12">
                    <div class="form-group">
                        <label for="company-column">{!! trans("lang.restaurant_address") !!}
                        </label>
                        <input type="text"  class="form-control" value='' placeholder="" id="address" name="address" placeholder="Adresse" />
                    </div>
                </div> -->
                <!-- <div class="col-md-6 col-12">
                    <div class="form-group updatecoordsLat">
                        <label for="company-column">Latitude</label>
                        <input type="text" id="updatedlatitude" required class="form-control"  name="latitude" placeholder="Latitude" />
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="form-group updatecoordsLong">
                        <label for="company-column">Longitude</label>
                        <input type="text"  id="updatedlongitude" required class="form-control" name="longitude" placeholder="Longitude" />
                    </div>
                </div>-->
                <div id="address-map-container" style="width:100%;height:400px; ">
                    <div style="width: 100%; height: 100%" id="map2"></div>
                </div> 

                @prepend('scripts')


                @endprepend


    </div>

    




<!-- <div class="col-12">
                    <div id="map2" ></div>
                </div> -->

@if($customFields)
    <div class="clearfix"></div>
    <div class="col-12 custom-field-container">
        <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
        {!! $customFields !!}
    </div>
@endif
<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.restaurant')}}</button>
    <a href="{!! route('superettes.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>


@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCIUe8WwDxmcJNDkiYmD0E_AUdJ41sgR2A&callback=initMap&v=weekly" async></script>
<script type="text/javascript">
    function initMap() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (p) {
                var Latlng = new google.maps.LatLng(p.coords.latitude, p.coords.longitude);
                var lat = document.getElementById("updatedlatitude").value;
                var lng = document.getElementById("updatedlongitude").value;
               
                if (lat != "" && lng != "") {
                    Latlng = {
                        lat: parseFloat(lat),
                        lng: parseFloat(lng)
                    };
                }
                setMap(Latlng);
            });
        } else {
            alert('Geo Location feature is not supported in this browser.');
        }
    }

    function validationlongitude(value) {
        return value.length >= 4 && /^(?=.)-?((0?[8-9][0-9])|180|([0-1]?[0-7]?[0-9]))?(?:\.[0-9]{1,20})?$/.test(value);
    }

    $("#updatedlatitude").keyup(function () {
        validateCoordsToInitMap("#updatedlatitude", "#updatedlongitude");
    });

    $("#updatedlongitude").keyup(function () {
        validateCoordsToInitMap("#updatedlatitude", "#updatedlongitude");
    });

    function validateCoordsToInitMap(idLatitude, idLongitude) {
        var getlatitude = $(idLatitude).val();
        var getlongitude = $(idLongitude).val();
        // validatelatitude("#updatedlatitude", "#validerUpdate", getlatitude);
        if (getlatitude.length > 0 && getlongitude.length > 0) {
            if (validerLatitude(getlatitude) && validationlongitude(getlongitude)) initMap();
        }
    }

    function setMap(Latlng) {

        const map2 = new google.maps.Map(document.getElementById("map2"), {
            zoom: 16,
            center: Latlng,
        });

        const markerr = new google.maps.Marker({
            position: Latlng,
            map: map2,
            title: "Click to zoom",
        });
    
        map2.addListener("click", (mapsMouseEvent) => {
            markerr.setPosition(mapsMouseEvent.latLng);
            document.getElementById("updatedlatitude").value = mapsMouseEvent.latLng.toJSON().lat;
            document.getElementById("updatedlongitude").value = mapsMouseEvent.latLng.toJSON().lng;
            let lati = document.getElementById("updatedlatitude").value;
            let longt = document.getElementById("updatedlongitude").value;
            var url = "";
            url = "{{url('map/getAdress')}}";
            $.ajax({
                method: "GET",
                url: url,
                data: {_token: "{{csrf_token()}}", lat: lati,lng:longt},
                success: function(data){
                    if(data && data[0].formatted_address){
                        document.getElementById("address").value = data[0].formatted_address
                    }
                }, 
                error: function(){
                    alert("error ");
                }
            })

        });
    }

</script>

@endpush