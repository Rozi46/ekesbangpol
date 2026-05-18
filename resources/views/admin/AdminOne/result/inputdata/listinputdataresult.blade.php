<?php $no = 0; ?>
@forelse($results as $view_data)
    <?php
        $no++;
        $id = str_replace('-','',$view_data['id']);
    ?>

    <tr>
        <td style="text-align:center;">{{ $no }}</td>
        <td style="text-align:center;">{{ number_format($view_data['heat_line']['heat']['nomor_seri'],0,"","") }}</td>
        <td style="text-align:left;">{{ $view_data['atlet']['nama'] ?? 'Belum ditentukan' }}</td>
        <td style="text-align:center;">{{ number_format($view_data['heat_line']['line_number'],0,"","") }}</td>
		<td style="text-align:center;">{{ $view_data['heat_line']['best_time'] ?? 'Belum ditentukan' }}</td>

        {{-- INPUT HASIL --}}
        @if($level_user['inputresult'] == 'Yes' && $view_data['status_data'] != 'Finish')         
            <td style="text-align:center;">
                <input type="text" class="input-hasil" data-id="{{$id}}" data-code="{{$view_data['code_data']}}" data-athlete="{{$view_data['atlet']['code_data']}}" value="{{$view_data['hasil']}}" style="width:95px;text-align:center;" placeholder="00:00.00"maxlength="8" >
            </td>         
        @else  
		    <td style="text-align:center;">{{ $view_data['hasil'] ?? '00:00.00' }}</td>
        @endif

        {{-- FOTO --}}
        <td style="text-align:center;">
            <img src="{{ $view_data['foto'] ? asset('/themes/admin/AdminOne/image/upload/'.$view_data['foto']) : asset('/themes/admin/AdminOne/image/no_image.png') }}" class="preview-foto" data-id="{{$id}}" style="width:150px;height:100px;object-fit:cover;cursor:pointer;border-radius:6px;border:1px solid #ddd;">
            <input type="file" class="input-foto" data-id="{{$id}}" accept="image/*" style="display:none;" >
                @if($level_user['inputresult'] == 'Yes' && $view_data['status_data'] != 'Finish')  
                    <div style="margin-top:6px;">
                        <button type="button" class="btn btn-info btn-sm btn-upload-foto" data-id="{{$id}}"> Upload Foto </button>
                    </div> 
                @endif
        </td>

        {{-- INPUT CATATAN --}}
        @if($level_user['inputresult'] == 'Yes' && $view_data['status_data'] != 'Finish')  
            <td style="text-align:center;">
                <input type="text" class="input-catatan" data-id="{{$id}}" data-code="{{$view_data['code_data']}}" data-athlete="{{$view_data['atlet']['code_data']}}" value="{{ $view_data['catatan'] ?? '' }}" style="width:100px;text-align:center;" maxlength="3" placeholder="DNF/DSQ/NS">
            </td>        
        @else  
		    <td style="text-align:center;">{{ $view_data['catatan'] ?? '' }}</td>
        @endif

        <td class="@if(($view_data['ranking'] ?? 0) == 1) rank-1 @elseif(($view_data['ranking'] ?? 0) == 2) rank-2 @elseif(($view_data['ranking'] ?? 0) == 3) rank-3 @endif" style="text-align:center;">{{ number_format($view_data['ranking'] ?? 0, 0,"",".") }}</td>
		<td style="text-align:center;">{{ number_format($view_data['poin'] ?? 0, 0,"",".") }}</td>
    </tr>
@empty
    <tr>
        <td colspan="20" style="text-align:center;padding:40px;"> Tidak ada data </td>
    </tr>
@endforelse

<!-- MODAL PREVIEW FOTO -->
<div id="modalPreviewFoto" style="
    display:none;
    position:fixed;
    z-index:9999;
    left:0;
    top:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.85);
    align-items:center;
    justify-content:center;
">
    <img id="imgPreviewBesar" src="" style="
        max-width:90%;
        max-height:90%;
        border-radius:8px;
        box-shadow:0 0 20px rgba(0,0,0,0.5);
    ">
</div>

<script>
    $(function(){
        /* FORMAT INPUT WAKTU */
        $(document).on('input','.input-hasil',function(e){
            let value = e.target.value.replace(/\D/g,'').substring(0,6);

            let formatted = '';
            if(value.length>0) formatted = value.substring(0,2);
            if(value.length>=3) formatted += ':'+value.substring(2,4);
            if(value.length>=5) formatted += '.'+value.substring(4,6);

            e.target.value = formatted;
        });

        /* SIMPAN HASIL (CHANGE) */
        $(document).on('change','.input-hasil',function(){
            const el = $(this);

            const hasil_up = el.val();
            const code_data = el.data('code');
            const code_athlete = el.data('athlete');
            const code_championship = $('input[name="code_championship"]').val();
            const code_event = $('input[name="code_event"]').val();

            loadingpage(2000);

            $.ajax({
                type:'POST',
                url:"saveresult?_token={{csrf_token()}}&token={{$request['token']}}&u={{$request['u']}}",
                data:{
                    code_data:code_data,
                    code_championship:code_championship,
                    code_event:code_event,
                    code_athlete:code_athlete,
                    hasil_up:hasil_up
                },
                success:function(res){
                    loadingpage(0);
                    if(res.status_message==='error'){
                        // alert('Data gagal disimpan');
                        SystemToast('danger','Data gagal disimpan'); 
                
                        // $('div[data-model="confirmasi"]').modal({backdrop: false});
                        // $('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-danger">Data gagal disimpan</div>');
                        // $('button[btn-action="action-confirmasi"]').remove();
                    }else{ 
                        SystemToast('success','Data berhasil disimpan');  

                        // $('div[data-model="confirmasi"]').modal({backdrop: false});
                        // $('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-success">Data berhasil disimpan</div>');
                        // $('button[btn-action="action-confirmasi"]').remove();
                    }
                }
            });
        });

        // /* OPEN FILE DIALOG */
        // $(document).on('click','.btn-upload-foto, .preview-foto',function(){
        //     const id = $(this).data('id');
        //     $('.input-foto[data-id="'+id+'"]').click();
        // });

        /* OPEN FILE DIALOG (TOMBOL SAJA) */
        $(document).on('click','.btn-upload-foto',function(){
            const id = $(this).data('id');
            $('.input-foto[data-id="'+id+'"]').click();
        });

        /* UPLOAD FOTO AJAX */
        $(document).on('change','.input-foto',function(){
            const fileInput = this;
            const id = $(this).data('id');
            const file = fileInput.files[0];

            if(!file) return;

            const row = $(this).closest('tr');
            const img = row.find('.preview-foto');

            /* preview langsung */
            const reader = new FileReader();
            reader.onload = e => img.attr('src', e.target.result);
            reader.readAsDataURL(file);

            const code_data = $('input[name="code_data"]').val();
            const code_championship = $('input[name="code_championship"]').val();
            const code_event = $('input[name="code_event"]').val();

            const formData = new FormData();
            formData.append('foto', file);
            formData.append('id', id);
            formData.append('code_data', code_data);
            formData.append('code_championship', code_championship);
            formData.append('code_event', code_event);
            formData.append('_token',"{{csrf_token()}}");
            formData.append('token',"{{$request['token']}}");
            formData.append('u',"{{$request['u']}}");

            loadingpage(2000);

            $.ajax({
                url:'uploadfotoresult',
                type:'POST',
                data:formData,
                processData:false,
                contentType:false,
                success:function(res){
                    loadingpage(0);
                    if(res.status_message==='success'){
                        img.attr('src',res.url);                        
                        SystemToast('success','Data berhasil di upload'); 
                    }else{
                        // alert('Upload gagal');
                        SystemToast('danger','Data gagal di upload');  
                
                        // $('div[data-model="confirmasi"]').modal({backdrop: false});
                        // $('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-danger">Upload gagal</div>');
                        // $('button[btn-action="action-confirmasi"]').remove();
                    }
                }
            });
        });

        /* PREVIEW FOTO BESAR */
        $(document).on('click','.preview-foto',function(){
            const src = $(this).attr('src');

            if(src.includes('no_image')) return; // cegah placeholder

            $('#imgPreviewBesar').attr('src',src);
            $('#modalPreviewFoto').fadeIn(200);
        });

        /* TUTUP SAAT KLIK BACKDROP */
        $('#modalPreviewFoto').on('click',function(){
            $(this).fadeOut(200);
        });

        /* VALIDASI CATATAN */
        $(document).on('input','.input-catatan',function(){
            let val = $(this).val().toUpperCase().replace(/[^A-Z]/g,'');
            val = val.substring(0,3);
            $(this).val(val);
        });

        /* SIMPAN CATATAN */
        $(document).on('change','.input-catatan',function(){
            const el = $(this);
            let val = el.val().toUpperCase();

            if(val && !['DNF','DSQ','NS'].includes(val)){
                // alert('Catatan hanya boleh: DNF, DSQ, atau NS');   
                // SystemAlert('success','Data berhasil disimpan'); 
                SystemToast('info','Catatan hanya boleh: DNF, DSQ, atau NS');   
                // SystemConfirm('Catatan hanya boleh: DNF, DSQ, atau NS', function(){
                //     // aksi jika YA
                // });
                
                // $('div[data-model="confirmasi"]').modal({backdrop: false});
                // $('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-danger">Catatan hanya boleh: DNF, DSQ, atau NS</div>');
                // $('button[btn-action="action-confirmasi"]').remove();

                el.val('');
                return;
            }

            const id = $(this).data('id');
            const code_data = el.data('code');
            const code_athlete = el.data('athlete');
            const code_championship = $('input[name="code_championship"]').val();
            const code_event = $('input[name="code_event"]').val();

            loadingpage(2000);

            $.ajax({
                type:'POST',
                url:"savecatatan?_token={{csrf_token()}}&token={{$request['token']}}&u={{$request['u']}}",
                data:{
                    id:id,
                    code_data:code_data,
                    code_championship:code_championship,
                    code_event:code_event,
                    code_athlete:code_athlete,
                    catatan:val
                },
                success:function(res){
                    loadingpage(0);
                    if(res.status_message==='error'){
                        // alert('Catatan gagal disimpan');
                        SystemToast('danger','Catatan gagal disimpan');
                
                        // $('div[data-model="confirmasi"]').modal({backdrop: false});
                        // $('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-danger">Catatan gagal disimpan</div>');
                        // $('button[btn-action="action-confirmasi"]').remove();
                    }else{                           
                        SystemToast('success','Catatan berhasil disimpan');         
                        // $('div[data-model="confirmasi"]').modal({backdrop: false});
                        // $('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-success">Catatan berhasil disimpan</div>');
                        // $('button[btn-action="action-confirmasi"]').remove();
                    }
                }
            });
        });
    });
</script>