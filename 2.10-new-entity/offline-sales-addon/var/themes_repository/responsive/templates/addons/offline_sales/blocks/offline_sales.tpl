{* block-description:block_offline_sales *}
<div class="grid-list">
    {foreach $offline_sales as $offline_sale_data}
        <div class="ty-column5">
            <div class="ty-grid-list__item offline-sales-sale">
                {if $offline_sale_data.main_pair.icon}
                    <a class="offline-sales-sale__image"
                       href="{fn_url("offline_sales.view?offline_sale_id={$offline_sale_data.offline_sale_id}")}"
                    >
                        {include file = "common/image.tpl"
                            images = $offline_sale_data.main_pair.icon
                            image_width = 250
                            image_height = 150
                        }
                    </a>
                {/if}

                <a href="{fn_url("offline_sales.view?offline_sale_id={$offline_sale_data.offline_sale_id}")}"
                   class="offline-sales-sale__title"
                >
                    {$offline_sale_data.title}
                </a>

                <div class="offline-sales-sale__store">
                    <a class="offline-sales-sale-store__name"
                       href="{fn_url("offline_sales.view?offline_sale_id={$offline_sale_data.offline_sale_id}")}"
                    >
                        <i class="ty-icon-pointer"></i>
                        {$offline_sale_data.store.name}
                    </a>
                </div>
            </div>
        </div>
    {/foreach}
</div>
