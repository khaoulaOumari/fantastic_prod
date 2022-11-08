@if($customFields)
<h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
<!-- Name Field -->
<div class="form-group row ">
  {!! Form::label('name', trans("lang.annonce_name"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.annonce_name_help")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.annonce_name_help") }}
    </div>
  </div>
</div>

<div class="form-group row " >
  {!! Form::label('type', trans("lang.annonce_type"),['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
      {!! Form::select('type', ['' => 'Choisir le type ?','2'=> 'Pop Up','1' => 'Slider','3'=> 'Vente Flash'], null, ['class' => 'select2 form-control'],['id' => 'type']) !!}
    <div class="form-text text-muted">{{ trans("lang.annonce_type") }}</div>
  </div>
</div>



<div class="form-group row " id="flashAds" style="display:none;">
  {!! Form::label('foods[]', trans("lang.annonce_foods"),['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
      {!! Form::select('foods[]', $promofoods, $promofoodselected, ['class' => 'select2 form-control templatingSelect2','multiple'=>'multiple']) !!}
    <div class="form-text text-muted">{{ trans("lang.annonce_foods") }}</div>
  </div>
</div>


<div class="form-group row" id="some"  style="display:none;">
  {!! Form::label('showing', trans("lang.annonce_showing"),['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
      {!! Form::select('showing', [''=> 'Afficher Où ?','Login' => 'Login','checkOut'=> 'checkOut','Pannier'=> 'Pannier'], null, ['class' => 'select2 form-control'],['id' => 'type']) !!}
    <div class="form-text text-muted">Moment d'affichage</div>
  </div>
</div>




<div class="form-group row" id="showImage"  style="display:none;">
  {!! Form::label('image', trans("lang.annonce_image"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <div style="width: 100%" class="dropzone image" id="image" data-field="image">
      <input type="hidden" name="image">
    </div>
    <!-- <a href="#loadMediaModal" data-dropzone="image" data-toggle="modal" data-target="#mediaModal" class="btn btn-outline-{{setting('theme_color','primary')}} btn-sm float-right mt-1">{{ trans('lang.media_select')}}</a> -->
    <div class="form-text text-muted w-50">
      {{ trans("lang.annonce_image_help") }}
    </div>
  </div>
</div>

<!-- Description Field -->
<!-- <div class="form-group row ">
  {!! Form::label('text', trans("lang.annonce_description"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::textarea('text', null, ['class' => 'form-control','placeholder'=>
     trans("lang.annonce_description_placeholder")  ]) !!}
    <div class="form-text text-muted">{{ trans("lang.annonce_description_placeholder") }}</div>
  </div>
</div> -->
</div>
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

<!-- Image Field -->



       
      <!-- <div class="form-group row ">
          {!! Form::label('start_date', trans("lang.start_date"),['class' => 'col-3 control-label text-right']) !!}
          <div class="col-9">
              {!! Form::date('start_date', null,  ['class' => 'form-control','autocomplete'=>'off'  ]) !!}
              <div class="form-text text-muted">
              </div>
          </div>
      </div>

      <div class="form-group row ">
          {!! Form::label('end_date', trans("lang.end_date"), ['class' => 'col-3 control-label text-right']) !!}
          <div class="col-9">
              {!! Form::date('end_date', null,  ['class' => 'form-control','autocomplete'=>'off'  ]) !!}
              <div class="form-text text-muted">
              </div>
          </div>
      </div> -->

      <div class="form-group row ">
        {!! Form::label('start_date', trans("lang.start_date"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('start_date', null,  ['class' => 'form-control datepicker','autocomplete'=>'off','placeholder'=>  trans("lang.start_date")  ]) !!}
          <div class="form-text text-muted">
            {{ trans("lang.start_date") }}
          </div>
        </div>
      </div>
      <div class="form-group row ">
        {!! Form::label('end_date', trans("lang.end_date"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('end_date', null,  ['class' => 'form-control datepicker','autocomplete'=>'off','placeholder'=>  trans("lang.end_date")  ]) !!}
          <div class="form-text text-muted">
            {{ trans("lang.end_date") }}
          </div>
        </div>
      </div>


        <div class="form-group row ">
            {!! Form::label('active', trans("lang.annonce_active"),['class' => 'col-3 control-label text-right']) !!}
            <div class="checkbox icheck">
                <label class="col-9 ml-2 form-check-inline">
                    {!! Form::hidden('active', 0) !!}
                    {!! Form::checkbox('active', 1, null) !!}
                </label>
            </div>
        </div>


    



@prepend('scripts')
<script type="text/javascript">
    var var15866134771240834480ble = '';
    @if(isset($annonce) && $annonce->hasMedia('image'))
    var15866134771240834480ble = {
        name: "{!! $annonce->getFirstMedia('image')->name !!}",
        size: "{!! $annonce->getFirstMedia('image')->size !!}",
        type: "{!! $annonce->getFirstMedia('image')->mime_type !!}",
        collection_name: "{!! $annonce->getFirstMedia('image')->collection_name !!}"};
    @endif
    var dz_var15866134771240834480ble = $(".dropzone.image").dropzone({
        url: "{!!url('uploads/store')!!}",
        addRemoveLinks: true,
        maxFiles: 1,
        init: function () {
        @if(isset($annonce) && $annonce->hasMedia('image'))
            dzInit(this,var15866134771240834480ble,'{!! url($annonce->getFirstMediaUrl('image','thumb')) !!}')
        @endif
        },
        accept: function(file, done) {
            dzAccept(file,done,this.element,"{!!config('medialibrary.icons_folder')!!}");
        },
        sending: function (file, xhr, formData) {
            dzSending(this,file,formData,'{!! csrf_token() !!}');
        },
        maxfilesexceeded: function (file) {
            dz_var15866134771240834480ble[0].mockFile = '';
            dzMaxfile(this,file);
        },
        complete: function (file) {
            dzComplete(this, file, var15866134771240834480ble, dz_var15866134771240834480ble[0].mockFile);
            dz_var15866134771240834480ble[0].mockFile = file;
        },
        removedfile: function (file) {
            dzRemoveFile(
                file, var15866134771240834480ble, '{!! url("annonces/remove-media") !!}',
                'image', '{!! isset($annonce) ? $annonce->id : 0 !!}', '{!! url("uplaods/clear") !!}', '{!! csrf_token() !!}'
            );
        }
    });
    dz_var15866134771240834480ble[0].mockFile = var15866134771240834480ble;
    dropzoneFields['image'] = dz_var15866134771240834480ble;
</script>
  <script type="text/javascript">


      function showHide(value){
        if(value==2){
          $("#some").show();
        }else{
          $("#some").hide();
        }

        if(value==1 || value==3){
          $("#showImage").hide();
        }else{
          $("#showImage").show();
        }

        if(value==1 || value==2){
          $("#flashAds").hide();
        }else{
          $("#flashAds").show();
        }
        if(!value || value==''){
          $("#flashAds").hide();
          $("#showImage").hide();
          $("#some").hide();
        }
      }
       


      $(window).on('load', function(){ 
        var value = $("#type").val()
        showHide(value)

      });
      

      $(document).ready(function(){

        $('#type').on('change',function(){
        var value = $(this).val();
        showHide(value)
       
      });

        function setCurrency (item) {
          if (!item.id) { return item.text; }

          // $.ajax({
          //   method: "GET",
          //   url: "{{url('foods/inconFood')}}",
          //   data: {_token: "{{csrf_token()}}", foodId: item.id},
          //   success: function(data){
          //     console.log(data)
          //     // var $item = $('<span><img class="rounded" style="width:50px" src='+data.data+' > '+ item.text +'</span>');
          //     // return $item;
          //   }, 
          //   error: function(){
          //         alert("error ");
          //   }
          // });

          // var $item = $('<span><img class="rounded" style="width:50px" src="http://127.0.0.1:8000/storage/app/public/34/conversions/FARINE-icon.jpg" > '+ item.text +'</span>');
          var $item = $('<span class="glyphicon glyphicon-' + item.element.value + '">' + item.text +'</span>');
		      return $item;
        };
        $(".templatingSelect2").select2({
          placeholder: "Choisir les produits à afficher?", //placeholder
          templateResult: setCurrency,
          templateSelection: setCurrency
        });
      })

      

  </script>
@endprepend
</div>
@if($customFields)
<div class="clearfix"></div>
<div class="col-12 custom-field-container">
  <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
  {!! $customFields !!}
</div>
@endif



<!-- Submit Field -->
<div class="form-group col-12 text-right">
  <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.annonce')}}</button>
  <a href="{!! route('annonces.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>



