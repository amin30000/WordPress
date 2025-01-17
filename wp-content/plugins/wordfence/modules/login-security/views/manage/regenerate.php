<?php
if (!defined('WORDFENCE_LS_VERSION')) { exit; }
/**
 * @var \WP_User $user The user being edited. Required.
 * @var int $remaining The number of unused recovery codes. Required.
 */
?>
<div class="wfls-block wfls-always-active wfls-flex-item-full-width">
	<div class="wfls-block-header wfls-block-header-border-bottom">
		<div class="wfls-block-header-content">
			<div class="wfls-block-title">
				<strong><?php esc_html_e('Recovery Codes', 'wordfence-2fa'); ?></strong>
			</div>
		</div>
	</div>
	<div class="wfls-block-content wfls-padding-add-bottom">
		<p id="wfls-recovery-code-count"><?php echo esc_html(sprintf($remaining == 1 ? __('%d unused recovery code remains. You may generate a new set by clicking below.', 'wordfence-2fa') : __('%d unused recovery codes remain. You may generate a new set by clicking below.', 'wordfence-2fa'), $remaining)); ?></p>
		<p class="wfls-center wfls-add-top"><a href="#" class="wfls-btn wfls-btn-default" id="wfls-recovery" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Generate New Codes', 'wordfence-2fa'); ?></a></p>
	</div>
</div>
<script type="text/x-jquery-template" id="wfls-tmpl-recovery-prompt">
	<?php
	echo \WordfenceLS\Model_View::create('common/modal-prompt', array(
		'title' => __('Generate New Recovery Codes', 'wordfence-2fa'),
		'message' => __('Are you sure you want to generate new recovery codes? Any remaining unused codes will be disabled.', 'wordfence-2fa'),
		'primaryButton' => array('id' => 'wfls-recovery-prompt-cancel', 'label' => __('Cancel', 'wordfence-2fa'), 'link' => '#'),
		'secondaryButtons' => array(array('id' => 'wfls-recovery-prompt-confirm', 'label' => __('Generate', 'wordfence-2fa'), 'link' => '#')),
	))->render();
	?>
</script>
<script type="application/javascript">
	(function($) {
		$(function() {
			$('#wfls-recovery').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				var prompt = $('#wfls-tmpl-recovery-prompt').tmpl({});
				var promptHTML = $("<div />").append(prompt).html();
				WFLS.panelHTML((WFLS.screenSize(500) ? '300px' : '400px'), promptHTML, {overlayClose: false, closeButton: false, className: 'wfls-modal', onComplete: function() {
					$('#wfls-recovery-prompt-cancel').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						WFLS.panelClose();
					});

					$('#wfls-recovery-prompt-confirm').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						var payload = {
							user: <?php echo (int) $user->ID; ?>,
						};

						WFLS.ajax(
							'wordfence_ls_regenerate',
							payload,
							function(response) {
								if (response.error) {
									WFLS.panelModal((WFLS.screenSize(500) ? '300px' : '400px'), '<?php echo esc_js(__('Error Generating New Codes', 'wordfence-2fa')); ?>', response.error);
								}
								else if (response.recovery) {
									$('#wfls-recovery-code-count').text(response.text);
									
									var message = '<p><?php echo \WordfenceLS\Text\Model_JavaScript::esc_js(sprintf(__('اگر دسترسی به دستگاه احراز هویت خود را از دست دادید، از یکی از این %d کدها برای ورود به سیستم استفاده کنید. کدها %d نویسه طولانی به اضافه فاصله اختیاری هستند. هر یک فقط یک بار قابل استفاده است.', 'wordfence-2fa'), \WordfenceLS\Controller_Users::RECOVERY_CODE_COUNT, \WordfenceLS\Controller_Users::RECOVERY_CODE_SIZE * 2)); ?></p><ul class="wfls-recovery-codes">';

									var recoveryCodeFileContents = '<?php echo \WordfenceLS\Text\Model_JavaScript::esc_js(sprintf(__('Two-Factor Authentication Recovery Codes - %s (%s)', 'wordfence-2fa'), preg_replace('~^https?://~i', '', home_url()), $user->user_login)); ?>' + "\r\n";
									recoveryCodeFileContents = recoveryCodeFileContents + "\r\n" + '<?php echo \WordfenceLS\Text\Model_JavaScript::esc_js(sprintf(__('Each line of %d letters and numbers is a single recovery code, with optional spaces for readability. To use a recovery code, after entering your username and password, enter the code like "1234 5678 90AB CDEF" at the 2FA prompt. If your site has a custom login prompt and does not show a 2FA prompt, you can use the single-step method by entering your password and the code together in the Password field, like "mypassword1234 5678 90AB CDEF". Your recovery codes are:', 'wordfence-2fa'), \WordfenceLS\Controller_Users::RECOVERY_CODE_SIZE * 2)); ?>' + "\r\n\r\n";
									for (var i = 0; i < response.recovery.length; i++) {
										message = message + '<li>' + response.recovery[i] + '</li>';
										recoveryCodeFileContents = recoveryCodeFileContents + response.recovery[i] + "\r\n";
									}

									message = message + "</ul>";

									message = message + "<p class=\"wfls-center\"><a href=\"#\" class=\"wfls-btn wfls-btn-default\" id=\"wfls-recovery-new-download\" target=\"_blank\" rel=\"noopener noreferrer\"><i class=\"dashicons dashicons-download\"></i> <?php echo \WordfenceLS\Text\Model_JavaScript::esc_js(__('Download', 'wordfence-2fa')); ?></a></p>";


									WFLS.panelModalHTML((WFLS.screenSize(500) ? '300px' : '400px'), "<?php echo \WordfenceLS\Text\Model_JavaScript::esc_js(__('New Recovery Codes', 'wordfence-2fa')); ?>", message, {onComplete: function() {
										$('#wfls-recovery-new-download').on('click', function(e) {
											e.preventDefault();
											e.stopPropagation();
											saveAs(new Blob([recoveryCodeFileContents], {type: "text/plain;charset=" + document.characterSet}), '<?php echo \WordfenceLS\Text\Model_JavaScript::esc_js(preg_replace('~^https?://~i', '', home_url()) . '_' . $user->user_login . '_recoverycodes.txt'); ?>');
										});
									}});
								}

								WFLS.panelClose(); //The prompt
							},
							function(error) {
								WFLS.panelModal((WFLS.screenSize(500) ? '300px' : '400px'), '<?php echo \WordfenceLS\Text\Model_JavaScript::esc_js(__('Error Generating New Codes', 'wordfence-2fa')); ?>', '<?php echo \WordfenceLS\Text\Model_JavaScript::esc_js(__('An error was encountered while trying to generate new recovery codes. Please try again.', 'wordfence-2fa')); ?>');
								WFLS.panelClose(); //The prompt
							}
						);
					});
				}});
			});
		});
	})(jQuery);
</script> 