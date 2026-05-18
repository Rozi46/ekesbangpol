
                            <div class="col-xl-12 col_act_page_main text-right">
                                <div class="table-action-bar">  
                                    <input type="text" id="app_load" style="display:none;" value="{{ $url_active }}" />
                                    <input type="text" id="searchInput" class="search" name="key-search-ajax" placeholder="Cari data..." style="display:none;" />

                                    <button type="button" btn="openSearchajax" class="btn btn-default btn_nav" onclick="openSearchAjax()" title="Cari Data"><i class="fa fa-search"></i></button>

                                    <input type="text" class="in_btn" id="countvdajax" value="{{ $count_vd }}" title="Jumlah Data Perpage"/>
                                    <span>/ <span id="totalData">0</span></span>

                                    <button type="button" id="btnFirst" class="btn btn-default btn_nav"><i class="fa fa-angle-double-left"></i></button>
                                    <button type="button" id="btnPrevPage" class="btn btn-default btn_nav"><span id="prevPageText">-</span></button>
                                    <button type="button" class="btn btn-default btn_nav active"><span id="currentPageText">1</span></button>
                                    <button type="button" id="btnNextPage" class="btn btn-default btn_nav"><span id="nextPageText">-</span></button>
                                    <button type="button" id="btnLast" class="btn btn-default btn_nav"><i class="fa fa-angle-double-right"></i></button>
                                </div>
                            </div>