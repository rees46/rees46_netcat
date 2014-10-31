<?
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($INCLUDE_FOLDER."index.php");

$input = nc_core('input');
$netshop = nc_netshop::get_instance();

$items = $input->fetch_get('items');
if (is_array($items)) {
	?>
	<div class="tpl-block-swiper">
	<div class="tpl-block-frontlayout-new">
		<div class="tpl-block-title tpl-block-title--size_l tpl-block-title--bright color-red">Рекомендовано rees46</div>
		<div class="tpl-block-swiper-container" style="height: 140px;">
	<div class="tpl-block-swiper-wrapper">
	<?
	foreach ($items as $item) {
		list($component_id, $item_id) = explode(":", $item);
		$item = nc_netshop_item::by_id($component_id, $item_id);

		if (!$item || !$item['Sub_Class_ID']) {
			continue;
		}
		?>

		<div class="tpl-block-swiper-item" style="width: 290px;">
			<!-- Карточка-->
			<div class=" tpl-block-cardbox tpl-block-cardbox--mini">
				<a class="tpl-block-cardbox-link" href="<?= nc_message_link($item['Message_ID'], $item['Class_ID']); ?>">
					<!-- Заголовок-->
					<div class="tpl-block-title tpl-block-title--size_ml">
						<div class="tpl-field-title">
							<?= $item['Vendor']; ?> <?= $item['Name']; ?>
						</div>
					</div>
					<!-- Описание-->
					<div class="tpl-field-description"><?= $item['Type']; ?></div>
					<!-- Картинка-->
					<div class="tpl-field-image"><img src="<?= $item['BigImage']; ?>"></div>
				</a>
				<!-- Рейтинг-->
				<div class="tpl-block-rating">
					<?php $rate = $item['RateCount'] ? $item['RateTotal'] / $item['RateCount'] : 0; ?>
					<?php for ($i = 1; $i <= 5; $i++) { ?>
						<?
						$class_name = 'icon-star';
						if ($rate < $i && $rate > ($i - 1)) {
							$class_name .= '-half-alt';
						} else if ($rate < $i) {
							$class_name .= '-empty';
						}
						?>
						<a href="<?= $item->get_rate_link($i); ?>" rel="nofollow"><div class="tpl-block-rating-item"><i class="<?= $class_name; ?>"></i></div></a>
					<? } ?>
				</div>
				<!-- Цена-->
				<div class="tpl-field-cost"><?= $item['ItemPriceF']; ?></div>
			</div>
		</div>
	<?
	}
	?>
	</div>
	</div>
	</div>
	</div>
<?
}

?>