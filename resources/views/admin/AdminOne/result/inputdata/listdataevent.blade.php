<?php $no = 0;?>
@forelse($results as $view_data)
    <?php
        $no++ ;
        $id = $view_data['id'];
        $id = str_replace('-','',$id);
        $hasil = $view_data['hasil'];
    ?>
    <tr>
        <td style="text-align:center;" id="hg_td">{{$no}}</td>
        <td style="text-align:center;">{{ number_format($view_data['heat']['nomor_seri'],0,"","") }}</td>
        <td style="text-align:left;">{{$view_data['atlet']['nama']}}</td>
        <td style="text-align:center;">{{ number_format($view_data['line_number'],0,"","") }}</td>
        <td style="text-align:center;">{{$view_data['best_time']}}</td>
        <td style="text-align:center;">
            <input 
                type="text" 
                name="new_hasil_{{$id}}" 
                value="{{$view_data['hasil']}}" 
                style="width: 95px; text-align:center;" 
                onkeypress="return goodchars(event,'0123456789:.',this)"
            >
        </td>
        <td style="text-align:center;">Foto Hasil</td>
        <td style="text-align:center;">{{$view_data['ranking']}}</td>
        <td style="text-align:center;">Point</td>           
    </tr>

    <script type="text/javascript">
        $(document).ready(function(){
            $('.bg_act_page_main button').prop({disabled:false});



            // $('input[name="new_hasil_{{$id}}"]').on('change', function () {

            //     let $input = $(this);

            //     let id = "{{ $view_data['id'] }}";
            //     let code_championship = $('select[name="code_championship"]').val();
            //     let code_event = $('select[name="code_event"]').val();
            //     let code_athlete = "{{ $view_data['atlet']['code_data'] }}";
            //     let hasil_up = $input.val();

            //     loadingpage(1);

            //     $.ajax({
            //         type: "POST",
            //         url: "saveresult?_token={{csrf_token()}}&token={{$request['token']}}&u={{$request['u']}}",
            //         data:{
            //             id:id,
            //             code_championship:code_championship,
            //             code_event:code_event,
            //             code_athlete:code_athlete,
            //             hasil_up:hasil_up
            //         },
            //         cache: false,
            //         success: function (res) {

            //             loadingpage(0);

            //             if (res.status_message !== 'success') {

            //                 $('div[data-model="confirmasi_data"]').modal({ backdrop: false });
            //                 $('div[data-model="confirmasi_data"] .modal-body')
            //                     .html('<div class="alert alert-danger">' + res.note + '</div>');

            //                 $input.val(hasil_up);
            //                 return;
            //             }

            //             // disable input setelah save
            //             $('[line="list_data"] input').prop('disabled', true);
            //             $('[line="list_data"] button').prop('disabled', true);
            //             $('.bg_act_page_main button').prop('disabled', true);

            //             // ambil code_data hasil
            //             let codeData = res.results?.[0]?.code_data ?? '';

            //             if (codeData) {
            //                 window.location.href = "viewresult?d=" + codeData;
            //             }
            //         },
            //         error: function (xhr) {
            //             loadingpage(0);

            //             $('div[data-model="confirmasi_data"]').modal({ backdrop: false });
            //             $('div[data-model="confirmasi_data"] .modal-body')
            //                 .html('<div class="alert alert-danger">Server error</div>');
            //         }
            //     });

            // });



            $('input[name="new_hasil_{{$id}}"]').change(function(){
                var id = "{{ $view_data['id'] }}";
                var code_championship = $('select[name="code_championship"]').val();
                var code_event = $('select[name="code_event"]').val();
                var code_athlete = "{{ $view_data['atlet']['code_data'] }}";
                var hasil_up = $('input[name="new_hasil_{{$id}}"]').val();

                loadingpage(2000);
                $.ajax({
                    type: "POST",
                    url: "saveresult?_token={{csrf_token()}}&token={{$request['token']}}&u={{$request['u']}}",
                    data:{
                        id:id,
                        code_championship:code_championship,
                        code_event:code_event,
                        code_athlete:code_athlete,
                        hasil_up:hasil_up
                    },
                    cache: false,
                    success: function(res){
                        loadingpage(0);
                        if(res.status_message == 'error'){
                            $('div[data-model="confirmasi_data"]').modal({backdrop: false});
                            $('div[data-model="confirmasi_data"] .modal-body').html('<div class="alert alert-danger">Data gagal disimpan.</div>');
                            $('button[btn-action="aciton-confirmasi"]').remove();
                            $('input[name="new_hasil_{{$id}}"]').val(hasil_up);
                        }else{
                            $('div[data-model="listproduk"]').modal('hide');
                            // $('select[name="data_perusahaan"]').prop({disabled:true});
                            // $('select[name="data_cabang"]').prop({disabled:true});
                            // $('input[name="code_transaksi"]').prop({disabled:true});
                            // $('input[name="tgl_transaksi"]').prop({disabled:true}).removeClass('pointer');
                            // $('select[name="supplier"]').prop({disabled:true});
                            // $('select[name="gudang"]').prop({disabled:true});
                            // $('input[name="no_pembelian"]').prop({disabled:true});
                            // $('input[name="no_do"]').prop({disabled:true});
                            $('.bg_act_page_main button').prop({disabled:true});
                            $('[line="list_data"] button').prop({disabled:true});
                            $('[line="list_data"] input').prop({disabled:true});
                            // window.location.reload();
                            window.location.href = "viewresult?d=" + res.code_data + "&code_championship=" + code_championship + "&code_event=" + code_event;

                        }
                    }
                });
            });


        });
    </script>
@empty
    <tr>
        <td style="text-align:center; padding: 20px; background-color: #FFFFFF; cursor: default; font-weight: 600; height: 250px; font-size: 14px;" colspan="20" >
            <i class="fa fa-shopping-bag"></i>
        </td>
    </tr>
    
    <script>
        $(document).ready(function(){
            $('.bg_act_page_main button').prop({disabled:true});
            $('[name="btn_cancel"]').prop({disabled:false});
            $('[onclick="BackPage()"]').prop({disabled:false});
        });
    </script>
@endforelse

@if($no > 0)
    @for ($i = 0; $i <= 0; $i++)
        <tr>
            <td class="blank" style="text-align:center;"></td>
            <td class="blank" style="text-align:center;"></td>
            <td class="blank" style="text-align:center;"></td>
            <td class="blank" style="text-align:center;"></td>
            <td class="blank" style="text-align:center;"></td>
            <td class="blank" style="text-align:center;"></td>
            <td class="blank" style="text-align:center;"></td>
            <td class="blank" style="text-align:center;"></td>
            <td class="blank" style="text-align:center;"></td>
        </tr>
    @endfor
@endif

<script type="text/javascript">
    $(document).ready(function(){
        var hg_td = $('#hg_td').height();
        $('.blank').css({"height":"40px","padding":"18px"});
    });
</script>