Установка
=========

Установка из ахрива (для CMS с лицензией)
-----------------------------------------

* Скачиваем с репозитория архив `rees46.tgz`
* Заходим в Админ-панель Netcat: Инструменты -> Установка модуля
* Открываем настройки модуля и вбиваем в поле `SHOP_ID=<KEY>` ключ магазина в rees46.

Установка для демоверсии
------------------------

* Скачиваем с репозитория архив `rees46.tgz`
* Распаковываем содержимое архива в директорию `/netcat/modules/rees46`
* Переносим все файлы из папки `/netcat/modules/rees46/netcat/modules/rees46` в `/netcat/modules/rees46`. Пустые папки можно удалить.
* Создаем строку в таблице Modules. В поле `SHOP_ID=<KEY>\nSHOP_SECRET=<SECRET>` заменяем сразу на ключ магазина в rees46.

		insert into Module ( Parameters, Inside_Admin, Checked, Module_Name, Keyword, Installed, Description) values ( 'SHOP_ID=<KEY>', '1', '1', 'NETCAT_MODULE_REES46', 'rees46', '1', 'NETCAT_MODULE_REES46_DESCRIPTION')
	
Настройка
=========

Подключение рекомендатора (общее)
---------------------------------

* Открываем в Админ-панели Разработка -> Макеты дизайна, выбираем слева "Интернет-магазин".
* Выбираем страницу "Титульная"
* Находим поле "Нижняя часть страницы (Footer):" и после надписи `%Footer` вставляем следующий код:

		<script src="//cdn.rees46.com/rees46_script2.js"></script>
		<script src="/netcat/modules/rees46/rees46.js"></script>
		<script type="text/javascript">
			$(function () {
				REES46.init('<?= $MODULE_VARS['rees46']['SHOP_ID'] ?>', {}, rees46_callback);
			});
		</script>

* Находим поле "Верхняя часть страницы (Header):" и в конец тега `<div class="tpl-block-layoutwidth">` перед закрывающимся тегом `</div>` вставляем поле для вывода рекомендаций `<div id="rees46_recomended"></div>`
* Выбираем страницу "Внутренняя"
* Находим поле "Нижняя часть страницы (Footer):" и вставляем туда следующий текст:

		<div id="rees46_recomended"></div>
		%Footer
		<script src="//cdn.rees46.com/rees46_script2.js"></script>
		<script src="/netcat/modules/rees46/rees46.js"></script>
		<script type="text/javascript">
			$(function () {
				REES46.init('<?= $MODULE_VARS['rees46']['SHOP_ID'] ?>', {}, rees46_callback);
			});
		</script>
	
Отлавливание событий
--------------------

* Просмотр товара: Открываем раздел Разработка -> Компоненты, слева в колонке ищем "Интернет магазин". Во все объекты с названием "Товар" нужно вставить следующий код в конец поля "Отображение объекта:":

		<script>$(document).ready(function(){ rees46_good_view('<?= $classID . ":" . $f_RowID ?>','<?= $item['Price'] ?>','<?= $item['Sub_Class_ID'] ?>') });</script>
	
* Добавление в корзину: открываем в редакторе файл `/netcat_template/template/85/assets/js/custom.js`, находим в нем функцию `processCartResponse` и добаляем в тело функции следующий код:
 
		function processCartResponse(response, itemIds) {
			for(var i in itemIds) {
				REES46.pushData('cart', {
					item_id: i,
					price: response.Items[i].ItemPrice,
					is_available: '1'
				});
			}
			
			...
		}

* Удаление из корзины: открываем в редакторе файл `/netcat/modules/netshop/classes/cart.php`, находим в нем функцию `remove_item` и добавляем необходимый код:

		public function remove_item($component_id, $item_id) {
			$component_id = (int)$component_id;
			$item_id = (int)$item_id;
			
			//REES46
			setcookie("rees46_track_remove_from_cart", json_encode(array(
				"item_id" => $component_id . ":" . $item_id
			)), time() + 86400, "/");
			
			...
		}
		
* Создание заказа: открываем в редакторе файл `/netcat/modules/netshop/classes/cart.php`, находим в нем функцию `checkout` и добавляем необходимый код:

		public function checkout(nc_netshop_order $order) {
			...
		
			$rees46_items = array();
			foreach ($this->get_items() as $item) {
				$rees46_items[] = array(
					"item_id" => $item['Class_ID'] . ":" . $item['Message_ID'],
					"amount" => $item['Qty']
				);
	
				...
			}
			setcookie("rees46_track_purchase", json_encode(array(
				"items" => $rees46_items,
				"order_id" => $order_id
			)), time() + 86400, "/");
		
			...
		}
