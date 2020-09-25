<div id="offline_sales_{$block.block_id}">
    {include file="common/pagination.tpl"}

    {if $offline_sales}
        {foreach $offline_sales as $offline_sale_data}
            <div class="offline-sales-sale">
                <a href="{fn_url("offline_sales.view?offline_sale_id={$offline_sale_data.offline_sale_id}")}">
                    <h2 class="offline-sales-sale__title">
                            {$offline_sale_data.title}
                    </h2>
                </a>

                {if $offline_sale_data.main_pair.detailed}
                    <a class="offline-sales-sale__image"
                       href="{fn_url("offline_sales.view?offline_sale_id={$offline_sale_data.offline_sale_id}")}"
                    >
                        {include file = "common/image.tpl"
                            images = $offline_sale_data.main_pair.detailed
                        }
                    </a>
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
                            {$offline_sale_data.description|strip_tags|truncate:450:"..." nofilter}

                            <a class="offline-sales-sale__read-more"
                               href="{fn_url("offline_sales.view?offline_sale_id={$offline_sale_data.offline_sale_id}")}"
                            >
                                {__("offline_sales.read_more")}
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        {/foreach}
    {else}
        <p class="ty-no-items">{__("no_data")}</p>
    {/if}

    {include file="common/pagination.tpl"}
</div>
