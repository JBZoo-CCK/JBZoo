<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$url        = $vars['link'];
$pagination = $vars['object'];
$bootstrap = $this->app->jbbootstrap;

$this->app->jbdebug->mark('layout::pagination::start');

if (!$pagination->getShowAll()) : ?>
    <div class="pagination">
        <ul class="pagination-list">
            <?php echo $bootstrap->paginate($pagination, $url); 
            ?>
        </ul>
    </div>


    <div>
						<button jbzoo-btnmore-pagination="button" class="sppb-btn  sppb-btn-default sppb-btn-rounded sppb-btn-rounded"
								data-total="<?=$pagination->pages()?>">
							Загрузить еще
						</button>
	</div>
					<script>
						document.addEventListener('DOMContentLoaded', function () {
							document.querySelectorAll('[jbzoo-btnmore-pagination="button"]') // Получаем все кнопки
								.forEach(function (button) {
									button.addEventListener('click', function (event) { // Ловим клик
										event.preventDefault(); // Глушим стандартное поведение

										let loading = document.querySelector('[jbzoo-ajax="loading"]');

										// Ставим на кнопку disabled и показываем лоадинг
										button.setAttribute('disabled', '');
										if (loading) {
											loading.style.display = '';
										}

										// Получаем блок куда будем вставлять итемы, максимально точный селектор.
										let blockSelector = '.jbzoo .flexcatdiv',
											block = document.querySelector(blockSelector);

										// Считаем кол-во итемов селектор тоже должен быть точным
                                        let itemSelector = '.jbzoo .flexcatdiv article',
                                        offset = block.querySelectorAll(itemSelector).length;

										// Убираем кнопку если не нужна и останавливаем функцию
										if (offset > parseInt(button.getAttribute('data-total'))) {
											button.remove();
											loading.style.display = 'none' // Скрываем лоадинг;
											return;
										}


     // Формируем адрес новой страницы
let url = new URL(window.location.href);
let pathParts = url.pathname.split('/').filter(part => part !== '');
let currentPage = parseInt(pathParts[pathParts.length - 1]);

if (isNaN(currentPage)) {
    currentPage = 1; // Если текущая страница не является числом, начинаем с первой страницы
} else {
    // Если текущая страница является числом, удаляем её из пути
    pathParts.pop();
}

let nextPage = currentPage + 1;
pathParts.push(nextPage.toString());
url.pathname = '/' + pathParts.join('/');

// Отправляем запрос
Joomla.request({
    url: url.toString(),
    method: 'GET',
    onSuccess: (response) => {
        let newHtml = document.createElement('div');
        newHtml.innerHTML = response;

        // Находим блок товаров
        let newBlock = newHtml.querySelector(blockSelector);

        // Начинаем добавлять итемы на нашу страницу
        newBlock.querySelectorAll(itemSelector).forEach(function (item) {
            block.appendChild(item);
        });

        // Убираем блокировку и скрываем лоадинг
        button.removeAttribute('disabled');
        if (loading) {
            loading.style.display = 'none';
        }

        // Обновляем текущую страницу
        pathParts[pathParts.length - 1] = nextPage.toString();
        window.history.pushState({}, '', '/' + pathParts.join('/'));
    },
    onError: (e) => {
        if (e.message && e.message !== '' && e.message !== 'Request aborted') {
            console.error(e.message);
        }
        // Скрываем лоадинг
        if (loading) {
            loading.style.display = 'none';
        }
    }
});






										console.log(url);
									})
								})
						});
					</script>


<?php endif;
$this->app->jbdebug->mark('layout::pagination::finish');