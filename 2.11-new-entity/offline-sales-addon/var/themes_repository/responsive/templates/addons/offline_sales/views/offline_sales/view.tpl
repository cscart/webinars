<div id="offline_sale_{$block.block_id}" class="offline-sales-sale">
    {if $offline_sale_data.main_pair.detailed}
        <div class="offline-sales-sale__image">
            {include file = "common/image.tpl"
                images = $offline_sale_data.main_pair.detailed
            }
        </div>
    {/if}

    <div class="row-fluid">
        <div class="span4">
            <div class="offline-sales-sale__store">
                <h3 class="offline-sales-sale-store__name">
                    {$offline_sale_data.store.name}
                </h3>
                <div class="offline-sales-sale-store__address">
                    <i class="ty-icon-pointer"></i>
                    {$offline_sale_data.store.pickup_address}
                </div>
                <div class="offline-sales-sale-store__work-hours">
                    <i class="ty-icon-calendar"></i>
                    {$offline_sale_data.store.pickup_time}
                </div>
                <div class="offline-sales-sale-store__phone">
                    <i class="ty-icon-star-empty"></i>
                    <a href="tel:{$offline_sale_data.store.pickup_phone}">{$offline_sale_data.store.pickup_phone}</a>
                </div>
            </div>
        </div>
        <div class="span12">
            <div class="offline-sales-sale__description">
                {$offline_sale_data.description nofilter}
            </div>
        </div>
    </div>

    <h3 class="offline-sales-sale__products-title">
        {__("offline_sales.discounted_products")}
    </h3>

    <div class="offline-sales-sale__products-list">
        {include file = "blocks/product_list_templates/products_multicolumns.tpl"
            products = $offline_sale_data.products
            columns = 4
            no_pagination = true
            no_sorting = true
        }
    </div>
</div>

{capture name="mainbox_title"}
    {$offline_sale_data.title}
{/capture}
