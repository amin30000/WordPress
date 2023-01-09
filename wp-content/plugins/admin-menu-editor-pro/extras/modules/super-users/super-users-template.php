<?php
/**
 * Variables set by ameModule when it outputs a template.
 *
 * @var string $moduleTabUrl
 * @see ameModule::getTabUrl
 */
?>
<div id="ame-super-user-settings">
	<h3>
		کاربران مخفی
		<a class="page-title-action" href="#"
		   data-bind="click: $root.selectHiddenUsers.bind($root), text: addButtonText">Add</a>
	</h3>

	<table class="wp-list-table widefat fixed striped">
		<thead>
		<tr>
			<th scope="col">نام کاربری</th>
			<th scope="col">نام</th>
			<th scope="col">نقش کاربری</th>
			<th class="ame-column-user-id num" scope="col">آی دی</th>
		</tr>
		</thead>

		<!-- ko if: (superUsers().length > 0) -->
		<tbody data-bind="foreach: superUsers">
		<tr>
			<td class="column-username">
				<span data-bind="html: avatarHTML"></span>
				<strong><a data-bind="text: userLogin, attr: {href: $root.getEditLink($data)}"></a></strong>

				<div class="row-actions">
					<span><a href="#" data-bind="click: $root.removeUser.bind($root, $data)">حذف</a></span>
				</div>
			</td>
			<td data-bind="text: displayName"></td>
			<td data-bind="text: $root.formatUserRoles($data)"></td>
			<td data-bind="text: userId" class="num"></td>
		</tr>
		</tbody>
		<!-- /ko -->

		<!-- ko if: (superUsers().length <= 0) -->
		<tbody>
		<tr>
			<td colspan="4">
				هیچ کاربری انتخاب نشده است. کلیک "<span data-bind="text: addButtonText"></span>" برای مخفی کردن یک یا چند کاربر
			</td>
		</tr>
		</tbody>
		<!-- /ko -->

		<tfoot>
		<tr>
			<th>نام کاربری</th>
			<th>نام</th>
			<th>نقش کاربری</th>
			<th class="ame-column-user-id num">آی دی</th>
		</tr>
		</tfoot>
	</table>

	<form action="<?php echo esc_attr(add_query_arg('noheader', 1, $moduleTabUrl)); ?>" method="post">
		<input type="hidden" name="settings" value="" data-bind="value: settingsData">
		<input type="hidden" name="action" value="ame_save_super_users">
		<?php
		wp_nonce_field('ame_save_super_users');
		submit_button('Save Changes', 'primary', 'submit', true);
		?>
	</form>

	<div class="metabox-holder">
	<div class="postbox ws_ame_doc_box" data-bind="css: {closed: !isInfoBoxOpen()}">
		<button type="button" class="handlediv button-link" data-bind="click: toggleInfoBox.bind($root)">
			<span class="toggle-indicator"></span>
		</button>
		<h2 class="hndle" data-bind="click: toggleInfoBox.bind($root)">چگونه کار می کند</h2>
		<div class="inside">
			<ul>
				<li>کاربران پنهان نمایش داده نمی شوند
					بر روی <a href="<?php echo esc_attr(self_admin_url('users.php')); ?>">یوزر &rightarrow; همه کاربران</a>
					برگه.
				</li>
				<li>کاربران عادی نمی توانند آنها را ویرایش یا حذف کنند.</li>
				<li>با این حال، آنها همچنان در مکان های دیگری مانند ستون "نویسنده" در صفحه "پست ها" و
پست ها و نظرات آنها به طور ویژه محافظت نمی شود.
				</li>
				<li>کاربران پنهان می توانند سایر کاربران پنهان را ببینند.
					<ul>
						<li>بنابراین اگر حساب کاربری خود را پنهان کنید، همچنان آن را در قسمت «همه کاربران» خواهید دید.
مگر اینکه به کاربر دیگری سوئیچ کنید.</li>
					</ul>
				</li>
			</ul>

		</div>
	</div>
	</div>

</div>