<?php
/**
 * @var string $moduleTabUrl
 */

$dragIconUrl = plugins_url('drag-indicator.svg', __FILE__);

if ( defined('AME_DISABLE_REDIRECTS') && constant('AME_DISABLE_REDIRECTS') ) {
	?>
	<div class="notice notice-warning">
		<p>
			تغییر مسیرهای سفارشی در حال حاضر غیرفعال هستند زیرا <code>AME_DISABLE_REDIRECTS</code> تنظیم شده است روز
			<code>true</code>.
		</p>
	</div>
	<?php
}
?>
<div id="ame-redirector-ui-root" data-bind="visible: isLoaded" style="display: none">
	<!-- A second level of tabs, uh oh! -->
	<ul data-bind="foreach: availableTriggers" role="tablist" class="ame-rui-trigger-selector ame-rui-sub-tabs">
		<li class="ame-rui-tab" data-bind="css: { 'ame-rui-active-tab': ($data.trigger === $root.selectedTrigger()) }">
			<a data-bind="
				text: label,
				click: $root.selectedTrigger.bind($root.selectedTrigger, $data.trigger),
				attr: {'data-text': label}"
			   class="ame-rui-tab-label"
			   role="tab"></a>
		</li>
	</ul>

	<div id="ame-rui-column-container">
		<div id="ame-rui-main-section">

			<!-- ko if: (selectedTrigger() === 'registration') -->
			<p>
				تغییر مسیر ثبت نام بلافاصله پس از ثبت نام یک حساب جدید انجام می شود
اما قبل از اینکه برای اولین بار وارد سیستم شوند. به طور پیش فرض، کاربر به آن هدایت می شود
صفحه "ایمیل خود را بررسی کنید".
			</p>
			<!-- /ko -->

			<!-- ko if: currentTriggerView().supportsUserSettings -->
			<h3>کاربران</h3>
			<div data-bind="
		template: {name: 'ame-redirect-list-template', data: {items: currentTriggerView().users}},
		visible: currentTriggerView().users().length > 0"></div>

			<!-- ko if: (userSelectionUi === 'dropdown') -->

			<p>
				<!-- ko if: (addableUsers().length > 0) -->
				<label for="ame-rui-add-user" class="screen-reader-text">افزودن کاربر جدید</label>
				<select id="ame-rui-add-user" class="ame-rui-add-actor-dropdown"
				        data-bind="options: addableUsers,
		         optionsText: 'userLogin',
		         value: selectedUserToAdd,
		         optionsCaption: 'افزودن کاربر',
		         hasFocus: userSelectorHasFocus"></select>
				<!-- /ko -->
				<!-- ko if: (addableUsers().length <= 0) -->
				<span class="description">لیست تمامی کاربران سایت شما</span>
				<!-- /ko -->
			</p>
			<!-- /ko -->
			<!-- ko if: (userSelectionUi === 'search') -->
			<form method="post" data-bind="submit: addEnteredUserLogin.bind($root)">
				<p>
					<label for="ame-rui-user-search-query" class="screen-reader-text">Search users</label>
					<input type="text" id="ame-rui-user-search-query" placeholder="Enter a username"
					       data-bind="
			        textInput: userLoginQuery,
			        ameRuiUserAutocomplete: { filter: filterUserAutocompleteResults.bind($root) }">
					<input type="button" class="button" id="ame-rui-add-user" value="Add user"
					       data-bind="enable: addUserButtonEnabled, click: addEnteredUserLogin.bind($root)">
				</p>
			</form>
			<!-- /ko -->
			<!-- /ko -->

			<!-- ko if: currentTriggerView().supportsRoleSettings -->
			<h3>نقش ها</h3>
			<div data-bind="
		template: {name: 'ame-redirect-list-template', data: {items: currentTriggerView().roles}},
		visible: currentTriggerView().roles().length > 0"></div>

			<p>
				<!-- ko if: (addableRoles().length > 0) -->
				<label for="ame-rui-add-role" style="display: none;">افزودن نقش کاربری</label>
				<select id="ame-rui-add-role" class="ame-rui-add-actor-dropdown"
				        data-bind="options: addableRoles,
		         optionsText: 'displayName',
		         value: selectedRoleToAdd,
		         optionsCaption: 'افزودن نقش کاربری',
		         hasFocus: roleSelectorHasFocus"></select>
				<!-- /ko -->
				<!-- ko if: (addableRoles().length <= 0) -->
				<span class="description">لیست تمامی نقش های کاربرام سایت شما</span>
				<!-- /ko -->
			</p>
			<!-- /ko -->

			<!-- ko if: currentTriggerView().supportsActorSettings -->
			<h3>پیشفرض</h3>
			<p>
				تنظیمات پیش فرض برای کاربرانی اعمال می شود که با هیچ یک از قوانین فوق مطابقت ندارند. میدان را ترک کن
خالی است تا به وردپرس یا سایر افزونه ها اجازه دهید تغییر مسیر را انتخاب کنند.
			</p>
			<!-- /ko -->
			<!-- ko ifnot: currentTriggerView().supportsActorSettings -->
			<h3>تمامی کاربران</h3>
			<!-- /ko -->
			<div data-bind="using: currentTriggerView().defaultRedirect" class="ame-rui-default-redirect-container">
				<div class="ame-rui-redirect">
					<div class="ame-rui-redirect-content">
						<ame-redirect-url-input params="redirect: $data, menuItems: $root.menuItems"
						                        class="ame-rui-url-template"></ame-redirect-url-input>

						<label class="ame-rui-shortcodes-enabled" title="Process shortcodes in the redirect URL">
							<input type="checkbox" data-bind="checked: shortcodesEnabled, enable: canToggleShortcodes">
							<span class="dashicons dashicons-shortcode"></span>
							<span class="ame-rui-button-label">فعال سازی کد کوتاه</span>
						</label>
					</div>
				</div>
			</div>

		</div>
		<div id="ame-rui-sidebar">
			<div id="ame-rui-main-actions">

			</div>
		</div>
	</div>

	<form class="ame-rui-save-form" method="post" data-bind="submit: saveChanges" action="<?php
	echo esc_attr(add_query_arg(array('noheader' => '1'), $moduleTabUrl));
	?>">
		<?php
		submit_button(
			null, 'primary', 'submit', true,
			[
				'data-bind' => 'disable: isSaving',
				'disabled'  => 'disabled',
			]
		);
		?>
		<input type="hidden" name="settings" value="" data-bind="value: settingsData">
		<input type="hidden" name="action" value="ame-save-redirect-settings">
		<?php wp_nonce_field('ame-save-redirect-settings'); ?>
		<input type="hidden" name="selectedTrigger" data-bind="value: selectedTrigger">
	</form>

	<label for="ame-rui-menu-items" style="display: none">آیتم های منوی مدیریت</label>
	<select name="ame-rui-menu-items" id="ame-rui-menu-items" size="10" style="display: none;"
	        data-bind="options: menuDropdownOptions, optionsText: 'title', value: selectedMenuDropdownItem">
	</select>
</div>

<div style="display: none;">
	<template id="ame-redirect-list-template">
		<div data-bind="sortable: {
			data: $data.items,
			allowDrop: false,
			options: {
				handle: '.ame-rui-drag-handle'
			}}"
		     class="ame-rui-redirect-list">
			<div class="ame-rui-redirect">
				<div class="ame-rui-drag-handle">
					<img src="<?php echo esc_attr($dragIconUrl); ?>" alt="Drag indicator" width="24">
				</div>
				<div class="ame-rui-redirect-content">
					<div class="ame-rui-actor">
						<label data-bind="text: displayName(), attr: {'for': inputElementId}"></label>
						<span class="ame-rui-missing-actor-indicator"
						      data-bind="
						      if: $root.isMissingActor(actor),
						      attr:{title: 'This ' + actorTypeNoun() + ' does not exist on the current site.'}">
							(missing)
						</span>
					</div>
					<ame-redirect-url-input params="redirect: $data, menuItems: $root.menuItems"
					                        class="ame-rui-url-template"></ame-redirect-url-input>
					<label class="ame-rui-shortcodes-enabled" title="Process shortcodes in the redirect URL">
						<input type="checkbox" data-bind="checked: shortcodesEnabled, enable: canToggleShortcodes">
						<span class="dashicons dashicons-shortcode"></span>
						<span class="ame-rui-button-label">فعال سازی کدکوتاه</span>
					</label>
					<div class="ame-rui-actions">
						<button class="button ame-rui-delete" title="Remove redirect"
						        data-bind="click: $parent.items.remove.bind($parent.items, $data)">
							<span class="dashicons dashicons-trash"></span>
							<span class="ame-rui-button-label">حذف</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</template>

	<template id="ame-redirect-url-component">
		<!--suppress HtmlFormInputWithoutLabel -->
		<input type="text"
		       data-bind="
		        value: displayValue,
		        attr: {'id' : redirect.inputElementId, 'readonly': isUrlReadonly},
				hasFocus: redirect.inputHasFocus,
				css: {'ame-rui-has-url-dropdown': redirect.urlDropdownEnabled}"
		       class="regular-text">
		<!-- ko if: redirect.urlDropdownEnabled -->
		<div class="ame-rui-url-dropdown-trigger">
			<span class="am-rui-trigger-icon"></span>
		</div>
		<!-- /ko -->
	</template>
</div>
