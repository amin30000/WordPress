<?php
/**
 * This is the HTML template for the plugin settings page.
 *
 * These variables are provided by the plugin:
 * @var array $settings Plugin settings.
 * @var string $editor_page_url A fully qualified URL of the admin menu editor page.
 * @var string $settings_page_url
 * @var string $db_option_name
 */

$currentUser = wp_get_current_user();
$isMultisite = is_multisite();
$isSuperAdmin = is_super_admin();
$formActionUrl = add_query_arg('noheader', 1, $settings_page_url);
$isProVersion = apply_filters('admin_menu_editor_is_pro', false);
?>

<?php do_action('admin_menu_editor-display_header'); ?>

	<form method="post" action="<?php echo esc_attr($formActionUrl); ?>" id="ws_plugin_settings_form">

		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					چه کسی می تواند به این افزونه دسترسی داشته باشد
				</th>
				<td>
					<fieldset>
						<p>
							<label>
								<input type="radio" name="plugin_access" value="super_admin"
									<?php checked('super_admin', $settings['plugin_access']); ?>
									<?php disabled( !$isSuperAdmin ); ?>>
								سوپر ادمین

								<?php if ( !$isMultisite ) : ?>
									<br><span class="description">
										معمولاً در یک سایت نصب می شود
همانند نقش مدیر.
									</span>
								<?php endif; ?>
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="plugin_access" value="manage_options"
									<?php checked('manage_options', $settings['plugin_access']); ?>
									<?php disabled( !current_user_can('manage_options') ); ?>>
								هر کسی که قابلیت "manage_options" را دارد

								<br><span class="description">
									به طور پیش فرض فقط مدیران این قابلیت را دارند.
								</span>
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="plugin_access" value="specific_user"
									<?php checked('specific_user', $settings['plugin_access']); ?>
									<?php disabled( $isMultisite && !$isSuperAdmin ); ?>>
								فقط کاربر فعلی

								<br>
								<span class="description">
									Login: <?php echo $currentUser->user_login; ?>,
								 	user ID: <?php echo get_current_user_id(); ?>
								</span>
							</label>
						</p>
					</fieldset>

					<p>
						<label>
							<input type="checkbox" name="hide_plugin_from_others" value="1"
								<?php checked( $settings['plugins_page_allowed_user_id'] !== null ); ?>
								<?php disabled( !$isProVersion || ($isMultisite && !is_super_admin()) ); ?>
							>
							"ویرایشگر منوی مدیریت" را مخفی کنید<?php if ( $isProVersion ) { echo ' Pro'; } ?>" ورود به صفحه "افزونه ها" از سایر کاربران
							<?php if ( !$isProVersion ) {
								echo '(Pro version only)';
							} ?>
						</label>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					تنظیمات چند سایتی
				</th>
				<td>
					<fieldset id="ame-menu-scope-settings">
						<p>
							<label>
								<input type="radio" name="menu_config_scope" value="global"
								       id="ame-menu-config-scope-global"
									<?php checked('global', $settings['menu_config_scope']); ?>
									<?php disabled(!$isMultisite || !$isSuperAdmin); ?>>
								Global &mdash;
								از تنظیمات منوی مدیریت یکسان برای همه سایت های شبکه استفاده کنید.
							</label><br>
						</p>


						<label>
							<input type="radio" name="menu_config_scope" value="site"
								<?php checked('site', $settings['menu_config_scope']); ?>
								<?php disabled(!$isMultisite || !$isSuperAdmin); ?>>
							Per-site &mdash;
							از تنظیمات مختلف منوی مدیریت برای هر سایت استفاده کنید.
						</label>
					</fieldset>
				</td>
			</tr>

			<?php do_action('admin-menu-editor-display_addons'); ?>

			<tr>
				<th scope="row">
					Modules
					<a class="ws_tooltip_trigger"
					   title="Modules are plugin features that can be turned on or off.
					&lt;br&gt;
					Turning off unused features will slightly increase performance and may help with certain compatibility issues.
					">
						<div class="dashicons dashicons-info"></div>
					</a>
				</th>
				<td>
					<fieldset>
						<?php
						global $wp_menu_editor;
						foreach ($wp_menu_editor->get_available_modules() as $id => $module) {
							if ( !empty($module['isAlwaysActive']) ) {
								continue;
							}

							$isCompatible = $wp_menu_editor->is_module_compatible($module);
							$compatibilityNote = '';
							if ( !$isCompatible && !empty($module['requiredPhpVersion']) ) {
								if ( version_compare(phpversion(), $module['requiredPhpVersion'], '<') ) {
									$compatibilityNote = sprintf(
										'Required PHP version: %1$s or later. Installed PHP version: %2$s',
										htmlentities($module['requiredPhpVersion']),
										htmlentities(phpversion())
									);
								}
							}

							echo '<p>';
							/** @noinspection HtmlUnknownAttribute */
							printf(
								'<label>
									<input type="checkbox" name="active_modules[]" value="%1$s" %2$s %3$s>
								    %4$s
								</label>',
								esc_attr($id),
								$wp_menu_editor->is_module_active($id, $module) ? 'checked="checked"' : '',
								$isCompatible ? '' : 'disabled="disabled"',
								!empty($module['title']) ? $module['title'] : htmlentities($id)
							);

							if ( !empty($compatibilityNote) ) {
								printf('<br><span class="description">%s</span>', $compatibilityNote);
							}

							echo '</p>';
						}
						?>
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row">رابط</th>
				<td>
					<p>
						<label>
							<input type="checkbox" name="hide_advanced_settings"
								<?php checked($settings['hide_advanced_settings']); ?>>
							گزینه های منوی پیشرفته را به طور پیش فرض مخفی کنید
						</label>
					</p>

					<?php if ($isProVersion): ?>
						<p>
						<label>
							<input type="checkbox" name="show_deprecated_hide_button"
								<?php checked($settings['show_deprecated_hide_button']); ?>>
							دکمه نوار ابزار "Hide (cosmetic)" را فعال کنید
						</label>
						<br><span class="description">
							این دکمه آیتم منوی انتخاب شده را بدون غیرقابل دسترسی پنهان می کند.
						</span>
						</p>
					<?php endif; ?>
				</td>
			</tr>

			<tr>
				<th scope="row">طرح رنگ ویرایشگر</th>
				<td>
					<fieldset>
						<p>
							<label>
								<input type="radio" name="ui_colour_scheme" value="classic"
									<?php checked('classic', $settings['ui_colour_scheme']); ?>>
								آبی
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="ui_colour_scheme" value="modern-one"
									<?php checked('modern-one', $settings['ui_colour_scheme']); ?>>
								مدرن
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="ui_colour_scheme" value="wp-grey"
									<?php checked('wp-grey', $settings['ui_colour_scheme']); ?>>
								سبز
							</label>
						</p>
					</fieldset>
				</td>
			</tr>

			<?php if ($isProVersion): ?>
			<tr>
				<th scope="row">نمایش نمادهای منوی فرعی</th>
				<td>
					<fieldset id="ame-submenu-icons-settings">
						<p>
							<label>
								<input type="radio" name="submenu_icons_enabled" value="always"
									<?php checked('always', $settings['submenu_icons_enabled']); ?>>
								همیشه
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="submenu_icons_enabled" value="if_custom"
									<?php checked('if_custom', $settings['submenu_icons_enabled']); ?>>
								فقط زمانی که به صورت دستی انتخاب شده باشد
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="submenu_icons_enabled" value="never"
									<?php checked('never', $settings['submenu_icons_enabled']); ?>>
								هرگز
							</label>
						</p>
					</fieldset>
				</td>
			</tr>

				<tr>
					<th scope="row">
						قابلیت مشاهده منوی جدید
						<a class="ws_tooltip_trigger"
						   title="This setting controls the default permissions of menu items that are
						    not present in the last saved menu configuration.
							&lt;br&gt;&lt;br&gt;
							This includes new menus added by plugins and themes.
							In Multisite, it also applies to menus that exist on some sites but not others.
							It doesn't affect menu items that you add through the Admin Menu Editor interface.">
							<div class="dashicons dashicons-info"></div>
						</a>
					</th>
					<td>
						<fieldset>
							<p>
								<label>
									<input type="radio" name="unused_item_permissions" value="unchanged"
										<?php checked('unchanged', $settings['unused_item_permissions']); ?>>
									بدون تغییر (پیش‌فرض)

									<br><span class="description">
										بدون محدودیت خاصی قابلیت مشاهده به افزونه بستگی دارد
که منوها را اضافه کرد.
									</span>
								</label>
							</p>

							<p>
								<label>
									<input type="radio" name="unused_item_permissions" value="match_plugin_access"
										<?php checked('match_plugin_access', $settings['unused_item_permissions']); ?>>
									نمایش فقط برای کاربرانی که می توانند به این افزونه دسترسی داشته باشند

									<br><span class="description">
										به طور خودکار تمام منوهای جدید و ناشناخته را از کاربران عادی پنهان می کند.
برای قابل مشاهده کردن منوهای جدید، باید آنها را به صورت دستی در ویرایشگر منو فعال کنید.
									</span>
								</label>
							</p>

						</fieldset>
					</td>
				</tr>
			<?php endif; ?>

			<tr>
			<th scope="row">
				موقعیت منو
				<a class="ws_tooltip_trigger"
				   title="This setting controls the position of menu items that are not present in the last saved menu
					configuration.
					&lt;br&gt;&lt;br&gt;
					This includes new menus added by plugins and themes.
					In Multisite, it also applies to menus that exist only on certain sites but not on all sites.
					It doesn't affect menu items that you add through the Admin Menu Editor interface.">
					<div class="dashicons dashicons-info"></div>
				</a>
			</th>
			<td>
				<fieldset>
					<p>
						<label>
							<input type="radio" name="unused_item_position" value="relative"
								<?php checked('relative', $settings['unused_item_position']); ?>>
						نظم نسبی را حفظ کنید

							<br><span class="description">
								تلاش برای قرار دادن آیتم های جدید در موقعیت های مشابه
همانطور که در منوی مدیریت پیش فرض قرار دارند.
							</span>
						</label>
					</p>

					<p>
						<label>
							<input type="radio" name="unused_item_position" value="bottom"
								<?php checked('bottom', $settings['unused_item_position']); ?>>
							دکمه

							<br><span class="description">
								موارد جدید را در پایین منوی مدیریت قرار می دهد.
							</span>
						</label>
					</p>

				</fieldset>
			</td>
			</tr>

			<?php
			//The free version lacks the ability to render deeply nested menus in the dashboard, so the nesting
			//options are hidden by default. However, if the user somehow acquires a configuration where this
			//feature is enabled (e.g. by importing config from the Pro version), the free version can display
			//and even edit that configuration to a limited extent.
			if ( $isProVersion || !empty($settings['was_nesting_ever_changed']) ):
				?>
				<tr>
					<th scope="row">
						منوی سه سطح
						<a class="ws_tooltip_trigger ame-warning-tooltip"
						   title="Caution: Experimental feature.&lt;br&gt;
						   This feature might not work as expected and it could cause conflicts with other plugins or themes.">
							<div class="dashicons dashicons-admin-tools"></div>
						</a>
					</th>
					<td>
						<fieldset>
							<?php
							$nestingOptions = array(
								'بپرس'                                     => null,
								'فعال' . ($isProVersion ? '' : ' (only in editor)') => true,
								'غیرفعال'                                             => false,
							);
							foreach ($nestingOptions as $label => $nestingSetting):
								?>
								<p>
									<label>
										<input type="radio" name="deep_nesting_enabled"
										       value="<?php echo esc_attr(json_encode($nestingSetting)); ?>"
											<?php
											if ( $settings['deep_nesting_enabled'] === $nestingSetting ) {
												echo ' checked="checked"';
											}
											?>>
										<?php echo $label; ?>
									</label>
								</p>
							<?php endforeach; ?>
						</fieldset>
					</td>
				</tr>
			<?php endif; ?>

			<tr>
				<th scope="row">
					WPML پشتیبانی
				</th>
				<td>
					<p>
						<label>
							<input type="checkbox" name="wpml_support_enabled"
								<?php checked($settings['wpml_support_enabled']); ?>>
							عناوین منوی ویرایش شده را با WPML قابل ترجمه کنید

							<br><span class="description">
								عناوین در بخش "Strings" در WPML ظاهر می شوند.
اگر از WPML یا افزونه ترجمه مشابه استفاده نمی کنید،
می توانید با خیال راحت این گزینه را غیرفعال کنید.
							</span>
						</label>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					لغو bbPress
				</th>
				<td>
					<p>
						<label>
							<input type="checkbox" name="bbpress_override_enabled"
								<?php checked($settings['bbpress_override_enabled']); ?>>
							از تنظیم مجدد قابلیت های نقش توسط bbPress جلوگیری کنید

							<br><span class="description">
							به طور پیش فرض، bbPress به طور خودکار هر تغییری را که در پویا ایجاد شده است، لغو می کند
نقش های bbPress. این گزینه را فعال کنید تا آن رفتار را لغو کرده و آن را ممکن کند
برای تغییر قابلیت های نقش bbPress.
							</span>
						</label>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">سطح پرحرفی خطا</th>
				<td>
					<fieldset id="ame-submenu-icons-settings">
						<p>
							<label>
								<input type="radio" name="error_verbosity" value="<?php echo WPMenuEditor::VERBOSITY_LOW ?>>"
									<?php checked(WPMenuEditor::VERBOSITY_LOW, $settings['error_verbosity']); ?>>
								پایین

								<br><span class="description">
									یک پیام خطای عمومی را بدون هیچ جزئیاتی نشان می دهد.
								</span>
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="error_verbosity" value="<?php echo WPMenuEditor::VERBOSITY_NORMAL; ?>>"
									<?php checked(WPMenuEditor::VERBOSITY_NORMAL, $settings['error_verbosity']); ?>>
								معمولی

								<br><span class="description">
									توضیح یک یا دو جمله ای را نشان می دهد. به عنوان مثال: «کاربر فعلی ندارد
قابلیت "manage_options" که برای دسترسی به آیتم منوی "تنظیمات" لازم است."
								</span>
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="error_verbosity" value="<?php echo WPMenuEditor::VERBOSITY_VERBOSE; ?>>"
									<?php checked(WPMenuEditor::VERBOSITY_VERBOSE, $settings['error_verbosity']); ?>>
								پرمخاطب

								<br><span class="description">
									مانند "عادی"، اما همچنین شامل فهرستی از تنظیمات منو و مجوزهایی است که
باعث شد منوی فعلی پنهان شود. برای رفع اشکال مفید است.
								</span>
							</label>
						</p>
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row">دیباگ</th>
				<td>
					<p>
					<label>
						<input type="checkbox" name="security_logging_enabled"
							<?php checked($settings['security_logging_enabled']); ?>>
						بررسی های دسترسی به منو انجام شده توسط افزونه را در هر صفحه مدیریت نمایش دهید
					</label>
					<br><span class="description">
						این می تواند به ردیابی مشکلات پیکربندی و کشف دلیل کمک کند
مجوزهای منوی شما آنطور که باید کار نمی کند.

						توجه: استفاده از این گزینه در یک سایت زنده به عنوان توصیه نمی شود
می تواند اطلاعات مربوط به تنظیمات منوی شما را نشان دهد.
					</span>
					</p>

					<p>
						<label>
							<input type="checkbox" name="force_custom_dashicons"
								<?php checked($settings['force_custom_dashicons']); ?>>
							سعی کنید نماد منو CSS را که توسط افزونه های دیگر اضافه شده است لغو کنید
						</label>
					</p>

					<p>
						<label>
							<input type="checkbox" name="compress_custom_menu"
								<?php checked($settings['compress_custom_menu']); ?>>
							فشرده سازی داده های پیکربندی منو که در پایگاه داده ذخیره شده است
						</label>
						<br><span class="description">
							به طور قابل توجهی اندازه را کاهش می دهد
							the <code><?php echo esc_html($db_option_name); ?></code> گزینه DB،
اما سربار رفع فشار را به هر صفحه اضافه می کند.
						</span>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">اطلاعات سرور</th>
				<td>
					<figure>
						<figcaption>گزارش خطای PHP:</figcaption>

						<code><?php
						echo esc_html(ini_get('error_log'));
						?></code>
					</figure>

					<figure>
						<figcaption>استفاده از حافظه PHP:</figcaption>

						<?php
						printf(
							'%.2f MiB of %s',
							memory_get_peak_usage() / (1024 * 1024),
							esc_html(ini_get('memory_limit'))
						);
						?>
					</figure>
				</td>
			</tr>
			</tbody>
		</table>

		<input type="hidden" name="action" value="save_settings">
		<?php
		wp_nonce_field('save_settings');
		submit_button();
		?>
	</form>

<?php do_action('admin_menu_editor-display_footer'); ?>

<script type="text/javascript">
	jQuery(function($) {
		//Set up tooltips
		$('.ws_tooltip_trigger').qtip({
			style: {
				classes: 'qtip qtip-rounded ws_tooltip_node ws_wide_tooltip'
			}
		});
	});
</script>