<?php
$lang['config_info'] = 'Geschäfts-Einstellungen';

$lang['config_address'] = 'Geschäfts-Adresse';
$lang['config_phone'] = 'Geschäfts-Telefonnummer';
$lang['config_prefix'] = 'Verkaufs-ID Präfix';
$lang['config_website'] = 'Geschäfts-Homepage';
$lang['config_fax'] = 'Faxnummer';
$lang['config_default_tax_rate'] = 'Standard-Steuersatz %';


$lang['config_company_required'] = 'Der &gt;&gt;Geschäfts-Name&lt;&lt; ist erforderlich';

$lang['config_phone_required'] = 'Die &gt;&gt;Geschäfts-Telefonnummer&lt;&lt; ist erforderlich';
$lang['config_sale_prefix_required'] = 'Der &gt;&gt;Rechnungsnummer-Präfix&lt;&lt; ist erforderlich';
$lang['config_default_tax_rate_required'] = 'Der &gt;&gt;Standard-Steuersatz %&lt;&lt; ist erforderlich';
$lang['config_default_tax_rate_number'] = 'Der &gt;&gt;Standard-Steuersatz %&lt;&lt; muss eine Zahl sein';
$lang['config_company_website_url'] = 'Die Geschäfts-Homepage ist keine gültige (http://...)';

$lang['config_saved_unsuccessfully'] = 'Die Konfiguration konnte nicht gespeichert werden. Im Demo-Modus sind keine Konfigurations-Änderungen erlaubt oder die Steuersätze konnten nicht gespeichert werden';
$lang['config_return_policy_required'] = 'Die Rücknahme-Richtlinie ist erforderlich';
$lang['config_print_after_sale'] = 'Nach dem Verkauf Beleg drucken';
$lang['config_automatically_email_receipt'] = 'Beleg automatisch per E-Mail versenden';
$lang['config_barcode_price_include_tax'] = 'Fügen Sie Steuer auf Barcodes';
$lang['disable_confirmation_sale'] = 'Bestätigungsdialog vor Verkauf deaktivieren';


$lang['config_currency_symbol'] = 'Währungs-Symbol';
$lang['config_backup_database'] = 'Datenbank-Backup';
$lang['config_restore_database'] = 'Datenbank wiederherstellen';

$lang['config_number_of_items_per_page'] = 'Anzahl der Artikel pro Seite';
$lang['config_date_format'] = 'Datumsformat';
$lang['config_time_format'] = 'Zeitformat';



$lang['config_database_optimize_successfully'] = 'Datenbank erfolgreich optimiert';
$lang['config_payment_types'] = 'Zahlungsarten';
$lang['select_sql_file'] = '.sql-Datei auswählen';
$lang['restore_heading'] = 'Hiermit können Sie die Datenbank wiederherstellen';
$lang['type_file'] = '.sql-Datei auf Ihrem Computer auswählen';
$lang['restore'] = 'wiederherstellen';
$lang['required_sql_file'] = 'Es wurde keine .sql-Datei ausgewählt';
$lang['restore_db_success'] = 'Datenbank wurde erfolgreich wiederhergestellt';
$lang['db_first_alert'] = 'Sind Sie sicher, dass die Datenbank wiederhergestellt werden soll?';
$lang['db_second_alert'] = 'Ihre aktuellen Daten gehen dabei verloren. Fortfahren?';
$lang['password_error'] = 'Falsches Passwort';
$lang['password_required'] = 'Das &gt;&gt;Passwortfeld&lt;&lt; ist erforderlich';
$lang['restore_database_title'] = 'Datenbank wiederherstellen';



$lang['config_environment'] = 'Umgebung';


$lang['config_sandbox'] = 'Sandbox';
$lang['config_production'] = 'Produktion';

$lang['config_default_payment_type'] = 'Standard-Zahlungsart';
$lang['config_speed_up_note'] = 'Nur empfehlenswert, wenn Sie mehr als 10.000 Artikel oder Kunden haben';
$lang['config_hide_signature'] = 'Unterschrift ausblenden';
$lang['config_round_cash_on_sales'] = 'Auf dem Beleg zu den nächsten 0.05 aufrunden';
$lang['config_customers_store_accounts'] = 'Kunden-Laden-Accounts aktivieren';
$lang['config_change_sale_date_when_suspending'] = 'Verkaufsdatum anpassen, wenn Verkauf zurückgestellt wird';
$lang['config_change_sale_date_when_completing_suspended_sale'] = 'Verkaufsdatum anpassen, wenn zurückgestellter Verkauf abgeschlossen wird';
$lang['config_price_tiers'] = 'Preis-Stufen';
$lang['config_add_tier'] = 'Stufe hinzufügen';
$lang['config_show_receipt_after_suspending_sale'] = 'Beleg anzeigen, nachdem Verkauf zurückgestellt wurde';
$lang['config_backup_overview'] = 'Backup-Übersicht';
$lang['config_backup_overview_desc'] = 'Das regelmäßige Erstellen eines Datenbank-Backups ist sehr wichtig, allerdings kann es bei großen Datenmengen mühsam werden. Wenn Sie viele Bilder, Artikel und Verkäufe gespeichert haben, kann das die Größe Ihrer Datenbank stark beeinflussen.';
$lang['config_backup_options'] = 'Wir bieten folgende vier Optionen an, um Ihnen die Entscheidung für die richtige Backupmethode zu erleichtern';
$lang['config_backup_simple_option'] = 'Auf "Datenbank-Backup" klicken. Dies wird versuchen, Ihre gesamte Datenbank als Datei herunterzuladen. Falls Sie einen weißen Bildschirm sehen oder die Datei nicht herunterladen können, versuchen Sie bitte eine der anderen Optionen';
$lang['config_backup_phpmyadmin_1'] = 'PHPMyAdmin ist ein beliebtes Tool zum Verwalten von Datenbanken. Falls Sie die heruntergeladene Version mit Installer verwenden, können Sie darauf zugreifen, indem Sie diesen Link verwenden:';
$lang['config_backup_phpmyadmin_2'] = 'Ihr Benutzername ist <b>root</b> und das Passwort ist auf den Wert gesetzt, den Sie während der Installation von %BRANDING_SHORT_NAME% angegeben haben. Wenn Sie angemeldet sind, wählen Sie Ihre Datenbank in dem Menü auf der linken Seite aus. Danach klicken Sie auf Export und senden dann das Formular ab.';
$lang['config_backup_control_panel'] = 'Falls Sie %BRANDING_SHORT_NAME% auf einem eigenen Server installiert haben, der eine Systemsteuerung hat, wie z.B. cpanel, suchen Sie nach dem Backup-Modul, welches Ihnen meistens die Möglichkeit bietet, Backups Ihrer Datenbank herunterzuladen.';
$lang['config_backup_mysqldump'] = 'Falls Sie Zugriff auf die Shell und mysqldump auf Ihrem Server haben, können Sie versuchen, das Backup über den unten stehenden Knopf auszuführen. Anderenfalls müssen Sie eine der anderen Optionen verwenden.';
$lang['config_mysqldump_failed'] = 'Das mysqldump-Backup ist fehlgeschlagen. Dies kann durch eine Server-Beschränkung passieren oder das Tool ist nicht installiert. Bitte versuchen Sie eine andere Backup-Option';



$lang['config_looking_for_location_settings'] = 'Sie suchen nach anderen Konfigurations-Möglichkeiten? Gehen Sie zum';
$lang['config_module'] = 'Modul';
$lang['config_automatically_calculate_average_cost_price_from_receivings'] = 'Durchschnitts-Einkaufspreis aus Lieferungen berechnen';
$lang['config_averaging_method'] = 'Durchschnitts-Berechnungs Methode';
$lang['config_historical_average'] = 'Vergangener Durchschnitt';
$lang['config_moving_average'] = 'Gleitender Durchschnitt';

$lang['config_hide_dashboard_statistics'] = 'Dashboard-Statistiken ausblenden';
$lang['config_hide_store_account_payments_in_reports'] = 'Laden-Account-Zahlungen in Berichten ausblenden';
$lang['config_id_to_show_on_sale_interface'] = 'Artikel-ID zur Anzeige in der Verkaufsoberfläche';
$lang['config_auto_focus_on_item_after_sale_and_receiving'] = 'Autofokus auf das Artikel-Feld in der Verkaufs-/Einkaufsoberfläche';
$lang['config_automatically_show_comments_on_receipt'] = 'Kommentare automatisch auf dem Beleg anzeigen';
$lang['config_hide_customer_recent_sales'] = 'Kürzliche Verkäufe eines Kunden ausblenden';
$lang['config_spreadsheet_format'] = 'Tabellen-Format';
$lang['config_csv'] = 'CSV';
$lang['config_xlsx'] = 'XLSX';
$lang['config_disable_giftcard_detection'] = 'Gutschein-Erkennung deaktivieren';
$lang['config_disable_subtraction_of_giftcard_amount_from_sales'] = 'Deaktiviere Gutschein-Abzug während eines Verkaufes';
$lang['config_always_show_item_grid'] = 'Artikel-Raster immer anzeigen';
$lang['config_legacy_detailed_report_export'] = 'Excel-Export veralteter Einträge als detaillierten Bericht aktivieren';
$lang['config_print_after_receiving'] = 'Nach dem Einkauf Beleg drucken';
$lang['config_company_info'] = 'Unternehmens-Information';


$lang['config_suspended_sales_layaways_info'] = 'Zurückgestellte Verkäufe';
$lang['config_application_settings_info'] = 'Anwendungs-Einstellungen';
$lang['config_hide_barcode_on_sales_and_recv_receipt'] = 'Barcode auf Beleg ausblenden';
$lang['config_round_tier_prices_to_2_decimals'] = 'Preis-Stufen auf zwei Dezimalstellen runden';
$lang['config_group_all_taxes_on_receipt'] = 'Alle Steuern auf dem Beleg gruppieren';
$lang['config_receipt_text_size'] = 'Beleg-Schriftgröße';
$lang['config_small'] = 'Klein';
$lang['config_medium'] = 'Mittel';
$lang['config_large'] = 'Groß';
$lang['config_extra_large'] = 'Extra groß';
$lang['config_select_sales_person_during_sale'] = 'Verkäufer während Verkauf wählen';
$lang['config_default_sales_person'] = 'Standardmäßiger Verkäufer';
$lang['config_require_customer_for_sale'] = 'Kunde für Verkauf erforderlich';

$lang['config_hide_store_account_payments_from_report_totals'] = 'Laden-Account-Zahlungen in den Bericht-Gesamtsummen ausblenden';
$lang['config_disable_sale_notifications'] = 'Verkaufs-Mitteilungen deaktivieren';
$lang['config_id_to_show_on_barcode'] = 'ID, welche auf dem Barcode angezeigt wird';
$lang['config_currency_denoms'] = 'Währungs-Einheiten';
$lang['config_currency_value'] = 'Wert';
$lang['config_add_currency_denom'] = 'Währungs-Einheit hinzufügen';
$lang['config_enable_timeclock'] = 'Zeiterfassung aktivieren';
$lang['config_change_sale_date_for_new_sale'] = 'Verkaufsdatum bei neuem Verkauf ändern';
$lang['config_dont_average_use_current_recv_price'] = 'Preise nicht mitteln, sondern momentanen Einkaufs-Preis verwenden';
$lang['config_number_of_recent_sales'] = 'Anzahl der kürzlich getätigten Verkäufe pro Kunde';
$lang['config_hide_suspended_recv_in_reports'] = 'Zurückgestellte Einkäufe in Berichten ausblenden';
$lang['config_calculate_profit_for_giftcard_when'] = 'Kalkuliere Gutschein-Gewinn, sobald';
$lang['config_selling_giftcard'] = 'Gutschein verkauft wird';
$lang['config_redeeming_giftcard'] = 'Gutschein eingelöst wird';
$lang['config_remove_customer_contact_info_from_receipt'] = 'Kunden-Kontakt-Informationen auf dem Beleg ausblenden';
$lang['config_speed_up_search_queries'] = 'Suchanfragen beschleunigen?';




$lang['config_redirect_to_sale_or_recv_screen_after_printing_receipt'] = 'Nach dem Druck des Belegs auf vorherige Seite umleiten';
$lang['config_enable_sounds'] = 'Sounds für Statusnachrichten aktivieren';
$lang['config_charge_tax_on_recv'] = 'Steuern auf Einkäufe berechnen';
$lang['config_report_sort_order'] = 'Sortierreihenfolge der Berichte';
$lang['config_asc'] = 'Den Ältesten zuerst';
$lang['config_desc'] = 'Den Neuesten zuerst';
$lang['config_do_not_group_same_items'] = 'Gleiche Artikel nicht gruppieren';
$lang['config_show_item_id_on_receipt'] = 'Artikel-ID auf dem Beleg anzeigen';
$lang['config_show_language_switcher'] = 'Sprach-Wechsler anzeigen';
$lang['config_do_not_allow_out_of_stock_items_to_be_sold'] = 'Den Verkauf von Artikeln, die nicht auf Lager sind, verbieten';
$lang['config_number_of_items_in_grid'] = 'Anzahl der Artikel pro Seite im Artikel-Raster';
$lang['config_edit_item_price_if_zero_after_adding'] = 'Artikelpreis nach dem Hinzufügen zum Verkauf ändern, wenn er 0 ist';
$lang['config_override_receipt_title'] = 'Beleg-Titel überschreiben';
$lang['config_automatically_print_duplicate_receipt_for_cc_transactions'] = 'Bei Kreditkarten-Transaktionen automatisch ein Beleg-Duplikat drucken';






$lang['config_default_type_for_grid'] = 'Standard-Typ für das Raster';
$lang['config_billing_is_managed_through_paypal'] = 'Die Abrechnung erfolgt über <a target="_blank" href="http://paypal.com">Paypal</a>. Sie können Ihre Informationen <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=BNTRX72M8UZ2E">hier</a> abrufen/ändern. <a href="http://%BRANDING_DOMAIN%/update_billing.php" target="_blank">Hier</a> finden Sie eine detaillierte Anleitung.';
$lang['config_cannot_change_language'] = 'Sprache kann nicht auf Anwendungsebene gespeichert werden. Der Standard-Admin-Mitarbeiter kann jedoch die Sprache mit dem Selektor im Kopf des Programms ändern';
$lang['disable_quick_complete_sale'] = 'Schnelle Verkaufs-Abwicklung deaktivieren';
$lang['config_fast_user_switching'] = 'Schnellen Benutzerwechsel aktivieren (ohne Passwort)';
$lang['config_require_employee_login_before_each_sale'] = 'Login eines Mitarbeiters vor jedem Verkauf erforderlich';
$lang['config_reset_location_when_switching_employee'] = 'Zurücksetzen Lage, wenn Mitarbeiter Schalt';
$lang['config_number_of_decimals'] = 'Anzahl der Dezimalstellen';
$lang['config_let_system_decide'] = 'Das System entscheiden lassen (empfohlen)';
$lang['config_thousands_separator'] = 'Tausendertrennzeichen';
$lang['config_enhanced_search_method'] = 'Erweiterte Suchmethode';
$lang['config_hide_store_account_balance_on_receipt'] = 'Laden-Account-Kontostand auf dem Beleg ausblenden';
$lang['config_decimal_point'] = 'Dezimalpunkt';
$lang['config_hide_out_of_stock_grid'] = 'Nicht vorrätige Artikel im Artikel-Raster ausblenden';
$lang['config_highlight_low_inventory_items_in_items_module'] = 'Artikel mit geringem Lagerbestand im Artikel-Modul hervorheben';
$lang['config_sort'] = 'Sortieren';
$lang['config_enable_customer_loyalty_system'] = 'Kunden-Treue-System aktivieren';
$lang['config_spend_to_point_ratio'] = 'Betrag, um Treuepunkte zu erhalten';
$lang['config_point_value'] = 'Anzeigewert der Treuepunkte (Nachkommastellen)';
$lang['config_hide_points_on_receipt'] = 'Treuepunkte auf dem Beleg ausblenden';
$lang['config_show_clock_on_header'] = 'Uhrzeit in der Kopfzeile anzeigen';
$lang['config_show_clock_on_header_help_text'] = 'Nur auf Wide-Screen-Bildschirmen sichtbar';
$lang['config_loyalty_explained_spend_amount'] = 'Betrag';
$lang['config_loyalty_explained_points_to_earn'] = 'Anzahl der Treuepunkte pro o.g. Betrag';
$lang['config_simple'] = 'Einfache Ansicht';
$lang['config_advanced'] = 'Erweiterte Ansicht';
$lang['config_loyalty_option'] = 'Treuepunkt-Programm-Optionen';
$lang['config_number_of_sales_for_discount'] = 'Anzahl der Verkäufe für Rabatt';
$lang['config_discount_percent_earned'] = 'Rabatt-Prozentsatz, wenn o.g. Anzahl Verkäufe erreicht wurde';
$lang['hide_sales_to_discount_on_receipt'] = 'Verkäufe für Rabatt auf dem Beleg ausblenden';
$lang['config_hide_price_on_barcodes'] = 'Preis auf Barcodes ausblenden';
$lang['config_always_use_average_cost_method'] = 'Immer Use Global Average Cost Preis für eine Kostenpreis des Verkaufs-Einzelteil. (NICHT überprüfen, wenn Sie wissen, was es bedeutet)';

$lang['config_test_mode_help'] = 'Verkäufe NICHT gespeichert';
$lang['config_require_customer_for_suspended_sale'] = 'Ist zum Zurückstellen von Verkäufen ein Kunde erforderlich?';
$lang['config_default_new_items_to_service'] = 'Standardmäßig neue Artikel als Service-Artikel festlegen?';






$lang['config_prompt_for_ccv_swipe'] = 'Aufforderung zur Eingabe des Sicherheitscodes, wenn die Kreditkarte durchgezogen wird';
$lang['config_disable_store_account_when_over_credit_limit'] = 'Laden-Account deaktivieren, wenn Kredit-Limit erreicht ist';
$lang['config_mailing_labels_type'] = 'Versandaufkleberformat';
$lang['config_phppos_session_expiration'] = 'Sitzungs-Timeout';
$lang['config_hours'] = 'Stunden';
$lang['config_never'] = 'Nie';
$lang['config_on_browser_close'] = 'Beim Schließen des Browserfensters';
$lang['config_do_not_allow_below_cost'] = 'Artikel dürfen nicht unter dem Einkaufspreis verkauft werden';
$lang['config_store_account_statement_message'] = 'Verwendungszweck, welcher auf dem Kontoauszug des Laden-Accounts angezeigt wird';
$lang['config_enable_markup_calculator'] = 'Aktivieren Mark Up-Rechner';
$lang['config_enable_quick_edit'] = 'Aktivieren Sie schnell bearbeiten zu verwalten Seiten';
$lang['config_show_orig_price_if_marked_down_on_receipt'] = 'Original zeigen Preis auf Empfang, wenn markiert nach unten';
$lang['config_confirm_error_messages_modal'] = 'Bestätigen Sie Fehlermeldungen modale Dialoge mit';
$lang['config_remove_commission_from_profit_in_reports'] = 'Entfernen Auftrag Gewinn in Berichten';
$lang['config_remove_points_from_profit'] = 'Entfernen Sie Punkte Erlösung aus Gewinn';
$lang['config_capture_sig_for_all_payments'] = 'Capture-Signatur für alle Verkäufe';
$lang['config_suppliers_store_accounts'] = 'Lieferanten Store Accounts';
$lang['config_currency_symbol_location'] = 'Währungssymbol Ort';
$lang['config_before_number'] = 'Vor Anzahl';
$lang['config_after_number'] = 'Nach Anzahl';
$lang['config_hide_desc_on_receipt'] = 'Ausblenden Beschreibung auf Empfang';
$lang['config_default_percent_off'] = 'Standard Prozent weg';
$lang['config_default_cost_plus_percent'] = 'Standard Cost Plus Prozent';
$lang['config_default_tier_percent_type_for_excel_import'] = 'Standard Tier Prozent Typ für Excel-Import';
$lang['config_override_tier_name'] = 'Außer Kraft setzen Tier Name auf der Quittung';
$lang['config_loyalty_points_without_tax'] = 'Treuepunkte verdient keine Steuern inklusive';
$lang['config_lock_prices_suspended_sales'] = 'Lock-Preise, wenn Desuspendieren Verkauf, auch wenn sie zu einem Tier gehören';
$lang['config_remove_customer_name_from_receipt'] = 'Entfernen Name des Kunden ab Eingang';
$lang['config_scale_1'] = 'UPC-12 4 Preisziffern';
$lang['config_scale_2'] = 'UPC-12 5 Preis Digits';
$lang['config_scale_3'] = 'EAN-13 5 Preisziffern';
$lang['config_scale_4'] = 'EAN-13 6 Preisziffern';
$lang['config_scale_format'] = 'Scale-Barcode-Format';
$lang['config_enable_scale'] = 'Aktivieren Skala';
$lang['config_scale_divide_by'] = 'Maßstab Preis Divide By';
$lang['config_logout_on_clock_out'] = 'Melden Sie sich automatisch aus, wenn Taktung aus';
$lang['config_user_configured_layaway_name'] = 'Außer Kraft setzen Layaway Namen';
$lang['config_use_tax_value_at_all_locations'] = 'Verwenden Sie Steuerwerte an allen Standorten';
$lang['config_enable_ebt_payments'] = 'Aktivieren EBT Zahlungen';
$lang['config_disable_markup_calculator'] = 'Preisspannen-Rechner deaktivieren';
$lang['config_disable_quick_edit'] = 'Schnellbearbeitung auf Verwaltungsseiten deaktivieren';
$lang['config_cancel_account'] = 'Konto schließen';
$lang['config_update_billing'] = 'Sie können Ihre Zahlungsinformationen durch Klick auf die unteren Buttons aktualisieren oder entfernen:';
$lang['config_include_child_categories_when_searching_or_reporting'] = 'Unterkategorien beim Suchen oder Auswerten mit einbinden';
$lang['config_item_id_auto_increment'] = 'Item ID Autoinkrement Startwert';
$lang['config_change_auto_increment_item_id_unsuccessful'] = 'Es gab einen Fehler auto_increment für item_id Wechsel';
$lang['config_item_kit_id_auto_increment'] = 'Artikel Kit ID Autoinkrement Startwert';
$lang['config_sale_id_auto_increment'] = 'Verkauf ID Autoinkrement Startwert';
$lang['config_receiving_id_auto_increment'] = 'Empfangen ID Autoinkrement Startwert';
$lang['config_change_auto_increment_item_kit_id'] = 'Es gab einen Fehler zu ändern auto_increment für Iitem_kit_id';
$lang['config_change_auto_increment_sale_id'] = 'Es gab einen Fehler auto_increment für sale_id Wechsel';
$lang['config_change_auto_increment_receiving_id'] = 'Es gab einen Fehler auto_increment für receiving_id Wechsel';
$lang['config_auto_increment_note'] = 'Sie können nur Autoinkrement Werte erhöhen. Aktualisieren von ihnen werden nicht-IDs für Artikel, Artikel-Kits, Vertrieb oder receivings auswirken, die bereits vorhanden sind.';

$lang['config_online_price_tier'] = 'Online-Preis Tier';
$lang['config_woo_api_key'] = 'WooCommerce API Key';
$lang['config_email_settings_info'] = 'Email Einstellungen';

$lang['config_last_sync_date'] = 'Datum der letzten Synchronisierung';
$lang['config_sync'] = 'Synchronisieren';
$lang['config_smtp_crypto'] = 'SMTP-Verschlüsselung';
$lang['config_email_protocol'] = 'Senden von Mail-Protokoll';
$lang['config_smtp_host'] = 'SMTP-Server-Adresse';
$lang['config_smtp_user'] = 'E-Mail-Addresse';
$lang['config_smtp_pass'] = 'E-Mail Passwort';
$lang['config_smtp_port'] = 'SMTP-Port';
$lang['config_email_charset'] = 'Zeichensatz';
$lang['config_email_newline'] = 'Newline Zeichen';
$lang['config_email_crlf'] = 'CRLF';
$lang['config_smtp_timeout'] = 'SMTP Timeout';
$lang['config_send_test_email'] = 'Test Email senden';
$lang['config_please_enter_email_to_send_test_to'] = 'Bitte geben Sie E-Mail-Adresse Test E-Mail senden an';
$lang['config_email_succesfully_sent'] = 'E-Mail wurde erfolgreich gesendet';
$lang['config_taxes_info'] = 'Steuern';
$lang['config_currency_info'] = 'Währung';

$lang['config_receipt_info'] = 'Eingang';

$lang['config_barcodes_info'] = 'Barcodes';
$lang['config_customer_loyalty_info'] = 'Kundentreue';
$lang['config_price_tiers_info'] = 'Preis Tiers';
$lang['config_auto_increment_ids_info'] = 'ID-Nummern';
$lang['config_items_info'] = 'Artikel';
$lang['config_employee_info'] = 'Mitarbeiter';
$lang['config_store_accounts_info'] = 'Store Accounts';
$lang['config_sales_info'] = 'Der Umsatz';
$lang['config_payment_types_info'] = 'Bezahlmöglichkeiten';
$lang['config_profit_info'] = 'Gewinnermittlung';
$lang['reports_view_dashboard_stats'] = 'Dashboard anzeigen Statistik';
$lang['config_keyword_email'] = 'Email Einstellungen';
$lang['config_keyword_company'] = 'Unternehmen';
$lang['config_keyword_taxes'] = 'Steuern';
$lang['config_keyword_currency'] = 'Währung';
$lang['config_keyword_payment'] = 'Zahlung';
$lang['config_keyword_sales'] = 'Der Umsatz';
$lang['config_keyword_suspended_layaways'] = 'suspendiert Layaways';
$lang['config_keyword_receipt'] = 'Eingang';
$lang['config_keyword_profit'] = 'profitieren';
$lang['config_keyword_barcodes'] = 'Barcodes';
$lang['config_keyword_customer_loyalty'] = 'Kundentreue';
$lang['config_keyword_price_tiers'] = 'Preisstufen';
$lang['config_keyword_auto_increment'] = 'Start Autoinkrement-ID-Nummern-Datenbank';
$lang['config_keyword_items'] = 'Artikel';
$lang['config_keyword_employees'] = 'Mitarbeiter';
$lang['config_keyword_store_accounts'] = 'Shop-Konten';
$lang['config_keyword_application_settings'] = 'Anwendungseinstellungen';
$lang['config_keyword_ecommerce'] = 'E-Commerce-Plattform';
$lang['config_keyword_woocommerce'] = 'WooCommerce Einstellungen E-Commerce';
$lang['config_billing_info'] = 'Abrechnungsdaten';
$lang['config_keyword_billing'] = 'Abrechnung stornieren Update';
$lang['config_woo_version'] = 'WooCommerce Version';

$lang['sync_phppos_item_changes'] = 'Sync Artikel Änderungen';
$lang['config_sync_phppos_item_changes'] = 'Sync Artikel Änderungen';
$lang['config_import_ecommerce_items_into_phppos'] = 'Importieren von Objekten in %BRANDING_SHORT_NAME%';
$lang['config_sync_inventory_changes'] = 'Sync Bestandsänderungen';
$lang['config_export_phppos_tags_to_ecommerce'] = 'Export-Tags zu E-Commerce';
$lang['config_export_phppos_categories_to_ecommerce'] = 'Export Kategorien E-Commerce';
$lang['config_export_phppos_items_to_ecommerce'] = 'Export Artikel zu E-Commerce';
$lang['config_ecommerce_cron_sync_operations'] = 'E-Commerce-Sync Operationen';
$lang['config_ecommerce_progress'] = 'Sync Fortschritt';
$lang['config_woocommerce_settings_info'] = 'WooCommerce Einstellungen';
$lang['config_store_location'] = 'Geschäftsort';
$lang['config_woo_api_secret'] = 'WooCommerce API Geheimnis';
$lang['config_woo_api_url'] = 'WooCommerce API URL';
$lang['config_ecommerce_settings_info'] = 'E-Commerce-Plattform';
$lang['config_ecommerce_platform'] = 'Plattform auswählen';
$lang['config_magento_settings_info'] = 'Magento-Einstellungen';
$lang['confirmation_woocommerce_cron_cancel'] = 'Sind Sie sicher, dass Sie die Synchronisierung abbrechen?';
$lang['config_force_https'] = 'Erfordern https für Programm';

$lang['config_keyword_price_rules'] = 'Preisregeln';
$lang['config_disable_price_rules_dialog'] = 'Deaktivieren Preisregeln Dialog';
$lang['config_price_rules_info'] = 'Preisregeln';

$lang['config_prompt_to_use_points'] = 'Prompt Punkte zu verwenden, wenn verfügbar';



$lang['config_always_print_duplicate_receipt_all'] = 'Drucken Sie immer doppelte Quittung für alle Transaktionen aus';


$lang['config_orders_and_deliveries_info'] = 'Aufträge und Lieferungen';
$lang['config_delivery_methods'] = 'Liefermethoden';
$lang['config_shipping_providers'] = 'Versandanbieter';
$lang['config_expand'] = 'Erweitern';
$lang['config_add_delivery_rate'] = 'Liefermenge hinzufügen';
$lang['config_add_shipping_provider'] = 'Versandversand hinzufügen';
$lang['config_delivery_rates'] = 'Lieferkosten';
$lang['config_delivery_fee'] = 'Liefergebühr';
$lang['config_keyword_orders_deliveries'] = 'Bestellt Lieferungen';
$lang['config_delivery_fee_tax'] = 'Liefergebühr Steuer';
$lang['config_add_rate'] = 'Rate hinzufügen';
$lang['config_delivery_time'] = 'Lieferzeit in Tagen';
$lang['config_delivery_rate'] = 'Zustelltarif';
$lang['config_rate_name'] = 'Rate Name';
$lang['config_rate_fee'] = 'Bewerbungsgebühr';
$lang['config_rate_tax'] = 'Steuern zahlen';
$lang['config_tax_classes'] = 'Steuergruppen';
$lang['config_add_tax_class'] = 'Steuergruppe hinzufügen';

$lang['config_wide_printer_receipt_format'] = 'Wide Printer Receipt Format';

$lang['config_default_cost_plus_fixed_amount'] = 'Default Cost Plus Fester Betrag';
$lang['config_default_tier_fixed_type_for_excel_import'] = 'Standard-fester fester Betrag für Excel-Import';
$lang['config_default_reorder_level_when_creating_items'] = 'Default Reorder Level beim Erstellen von Items';
$lang['config_remove_customer_company_from_receipt'] = 'Kundennummer aus dem Beleg entfernen';

$lang['config_import_ecommerce_categories_into_phppos'] = 'Importieren Sie Kategorien in Phppos';
$lang['config_import_ecommerce_tags_into_phppos'] = 'Importiert Tags in Phppos';

$lang['config_shipping_zones'] = 'Versandzonen';
$lang['config_add_shipping_zone'] = 'Fügen Sie Versandzone hinzu';
$lang['config_no_results'] = 'Keine Ergebnisse';
$lang['config_zip_search_term'] = 'Geben Sie eine Postleitzahl ein';
$lang['config_searching'] = 'Suchen ...';
$lang['config_tax_class'] = 'Steuergruppe';
$lang['config_zone'] = 'Zone';

$lang['config_zip_codes'] = 'Postleitzahlen';
$lang['config_add_zip_code'] = 'Postleitzahl hinzufügen';
$lang['config_ecom_sync_logs'] = 'E-Commerce-Synchronisierungsprotokolle';
$lang['config_currency_code'] = 'Währungscode';

$lang['config_add_currency_exchange_rate'] = 'Währungswechselkurs hinzufügen';
$lang['config_currency_exchange_rates'] = 'Wechselkurse';
$lang['config_exchange_rate'] = 'Tauschrate';
$lang['config_item_lookup_order'] = 'Artikelsuche';
$lang['config_item_id'] = 'Artikel Identifikationsnummer';
$lang['config_reset_ecommerce'] = 'E-Commerce zurücksetzen';
$lang['config_confirm_reset_ecom'] = 'Sind Sie sicher, dass Sie E-Commerce zurücksetzen möchten? Dies wird nur php Punkt des Verkaufs zurücksetzen, damit Artikel nicht mehr verknüpft sind';
$lang['config_reset_ecom_successfully'] = 'Sie haben E-Commerce erfolgreich zurückgesetzt';
$lang['config_number_of_decimals_for_quantity_on_receipt'] = 'Anzahl der Dezimalstellen für Anzahl bei Empfang';
$lang['config_enable_wic'] = 'WIC aktivieren';
$lang['config_store_opening_time'] = 'Store Öffnungszeit';
$lang['config_store_closing_time'] = 'Speichern der Schließzeit';
$lang['config_limit_manual_price_adj'] = 'Begrenzung manuelle Preisanpassungen und Rabatte';
$lang['config_always_minimize_menu'] = 'Immer Minimieren Linke Seite Bar Menü';

$lang['config_emailed_receipt_subject'] = 'E-Mail-Empfangsgegenstand';
$lang['config_do_not_tax_service_items_for_deliveries'] = 'Steuern Sie keine Serviceartikel für Lieferungen';


$lang['config_do_not_show_closing'] = 'Zeigen Sie nicht den erwarteten Schlussbetrag beim Schließen des Registers an';

$lang['config_paypal_me'] = 'PayPal.me Benutzername';


$lang['config_show_barcode_company_name'] = 'Firmennamen auf Barcode anzeigen';
$lang['config_import_ecommerce_attributes_into_phppos'] = 'Importieren von Attributen in Phppos';
$lang['config_export_phppos_attributes_to_ecommerce'] = 'Exportieren von Attributen an E-Commerce';

$lang['config_sku_sync_field'] = 'SKU-Feld zum Synchronisieren mit';



$lang['config_overwrite_existing_items_on_excel_import'] = 'Vorhandene Objekte beim Excel-Import überschreiben';

$lang['config_do_not_force_http'] = 'Erzwingen Sie HTTP nicht, wenn dies für die EMV-Kreditkartenverarbeitung erforderlich ist';
$lang['config_add_suspended_sale_type'] = 'Ausgehender Verkaufstyp hinzufügen';
$lang['config_additional_suspend_types'] = 'Zusätzliche ausgesetzte Verkaufsarten';
$lang['config_remove_employee_from_receipt'] = 'Entfernen Sie den Mitarbeiternamen aus dem Beleg';
$lang['config_import_ecommerce_orders_into_phppos'] = 'Importieren Sie Bestellungen in %BRANDING_SHORT_NAME%';
$lang['import_ecommerce_orders_into_phppos'] = 'Bestellungen in PHP-Pos. Importieren';
$lang['config_hide_name_on_barcodes'] = 'Verstecken Sie den Namen auf Barcodes';


$lang['config_api_settings_info'] = 'API-Einstellungen';
$lang['config_keyword_api'] = 'API';
$lang['config_api_keys'] = 'API-Schlüssel';
$lang['config_api_key_ending_in'] = 'API-Schlüssel, der eingeht';
$lang['config_permissions'] = 'Berechtigungen';
$lang['config_last_access'] = 'Letzter Zugriff';
$lang['config_add_key'] = 'Fügen Sie den API-Schlüssel hinzu';
$lang['config_api_key'] = 'API-Schlüssel';
$lang['config_read'] = 'Lesen';
$lang['config_read_write'] = 'Lesen Schreiben';
$lang['config_submit_api_key'] = 'Möchten Sie diesen Schlüssel wirklich hinzufügen? Bitte vergewissern Sie sich, dass Sie den Schlüssel an den sicheren Ort kopiert haben, da er nicht mehr angezeigt wird.';
$lang['config_write'] = 'Schreiben';
$lang['config_api_key_confirm_delete'] = 'Möchten Sie diesen API-Schlüssel wirklich löschen?';
$lang['config_key_copied_to_clipboard'] = 'Schlüssel kopiert in Zwischenablage';

$lang['config_new_items_are_ecommerce_by_default'] = 'Neue Artikel sind standardmäßig E-Commerce';


$lang['config_new_items_are_ecommerce_by_default'] = 'Neue Artikel sind standardmäßig E-Commerce';

$lang['config_hide_description_on_sales_and_recv'] = 'Beschreibung für Verkaufs- und Empfangsschnittstellen ausblenden';





$lang['config_hide_item_descriptions_in_reports'] = 'Objektbeschreibung in Berichten ausblenden';





$lang['config_do_not_allow_item_with_variations_to_be_sold_without_selecting_variation'] = 'Lassen Sie keine Variation-Artikel verkaufen, ohne Variation zu wählen';



$lang['config_verify_age_for_products'] = 'Überprüfen Sie das Alter für Produkte';
$lang['config_default_age_to_verify'] = 'Zu überprüfendes Standardalter';




$lang['config_remind_customer_facing_display'] = 'Erinnern Sie den Mitarbeiter daran, die Kundenanzeige zu öffnen';

$lang['config_import_tax_classes_into_phppos'] = 'Importiere Steuerklassen in %BRANDING_SHORT_NAME%';
$lang['config_export_tax_classes_into_phppos'] = 'Steuerklassen in E-Commerce exportieren';
$lang['config_import_shipping_classes_into_phppos'] = 'Importieren Sie Versandklassen in %BRANDING_SHORT_NAME%';
$lang['config_disable_confirm_recv'] = 'Deaktivieren Sie die Bestätigung für den vollständigen Empfang';
$lang['config_minimum_points_to_redeem'] = 'Mindestanzahl an Punkten zum Einlösen';
$lang['config_default_days_to_expire_when_creating_items'] = 'Standardtage, die beim Erstellen von Elementen ablaufen';


$lang['config_quickbooks_settings'] = 'Quickbooks Einstellungen';
$lang['config_qb_sync_operations'] = 'Quickbooks-Synchronisierungsvorgänge';
$lang['config_import_quickbooks_items_into_phppos'] = 'Importieren Sie Objekte in %BRANDING_SHORT_NAME%';
$lang['config_export_phppos_items_to_quickbooks'] = 'Artikel in Quickbooks exportieren';
$lang['config_import_customers_into_phppos'] = 'Importieren Sie Kunden in %BRANDING_SHORT_NAME%';
$lang['config_import_suppliers_into_phppos'] = 'Importieren Sie Lieferanten in %BRANDING_SHORT_NAME%';
$lang['config_import_employees_into_phppos'] = 'Importieren Sie Mitarbeiter in %BRANDING_SHORT_NAME%';
$lang['config_export_employees_to_quickbooks'] = 'Mitarbeiter in Quickbooks exportieren';
$lang['config_export_sales_to_quickbooks'] = 'Verkauf nach Quickbooks exportieren';
$lang['config_export_receivings_to_quickbooks'] = 'Empfänge in Quickbooks exportieren';
$lang['config_export_customers_to_quickbooks'] = 'Exportieren Sie Kunden in Quickbooks';
$lang['config_export_suppliers_to_quickbooks'] = 'Lieferanten zu Quickbooks exportieren';
$lang['config_connect_to_qb_online'] = 'Verbinden Sie sich mit Quickbooks online';
$lang['config_refresh_tokens'] = 'Tokens aktualisieren';
$lang['config_reconnect_quickbooks'] = 'Erneut mit Quickbooks online verbinden';
$lang['config_reset_quickbooks'] = 'Quickbooks zurücksetzen';
$lang['config_qb_sync_logs'] = 'Quickbooks synchronisieren Protokolle';
$lang['config_quickbooks_progress'] = 'Quickbooks synchronisieren den Fortschritt';
$lang['config_last_qb_sync_date'] = 'Letztes Synchronisierungsdatum';
$lang['config_confirmation_qb_cron_cancel'] = 'Möchten Sie die Synchronisierung der Quickbooks wirklich abbrechen?';
$lang['config_confirmation_qb_cron'] = 'Sind Sie sicher, dass Sie Quickbooks synchronisieren möchten?';
$lang['config_confirm_reset_qb'] = 'Möchten Sie Quickbooks wirklich zurücksetzen? Dadurch werden Sie von Quickbooks getrennt.';
$lang['config_reset_qb_successfully'] = 'Sie haben Quickbooks erfolgreich zurückgesetzt';
$lang['config_export_phppos_categories_to_quickbooks'] = 'Kategorien von Phppos in Quickbooks exportieren';
$lang['config_create_payment_methods'] = 'Erstellen Sie Zahlungsmethoden in QB';


$lang['config_allow_scan_of_customer_into_item_field'] = 'Scannen von Kunden in Artikelfeld zulassen';
$lang['config_cash_alert_high'] = 'Alarm, wenn das Geld höher ist';
$lang['config_cash_alert_low'] = 'Warnen, wenn das Geld darunter ist';


$lang['config_sync_inventory_changes_qb'] = 'Inventaränderungen synchronisieren';

$lang['config_sort_receipt_column'] = 'Quittungsspalte sortieren';





$lang['config_show_tax_per_item_on_receipt'] = 'Zeigen Sie die Steuern pro Artikel bei Erhalt';





$lang['config_enable_timeclock_pto'] = 'Aktivieren Sie die bezahlte Zeit für die Zeituhr';


$lang['config_enable_timeclock_pto'] = 'Aktivieren Sie die bezahlte Zeit für die Zeituhr';

$lang['config_show_item_id_on_recv_receipt'] = 'Zeige Artikel-ID beim Empfang';





$lang['config_import_all_past_orders_for_woo_commerce'] = 'Importieren Sie ALLE vergangenen Bestellungen für WooCommerce';




$lang['config_enable_margin_calculator'] = 'Aktivieren Sie den Margenrechner';










$lang['config_hide_barcode_on_barcode_labels'] = 'Barcode auf Etiketten ausblenden';



$lang['config_do_not_delete_saved_card_after_failure'] = 'Löschen Sie die gespeicherte Karte NICHT nach einem Fehler';





$lang['config_capture_internal_notes_during_sale'] = 'Erfassen Sie interne Notizen während des Verkaufs';





$lang['config_hide_prices_on_fill_sheet'] = 'Preise auf Erfüllungsbogen ausblenden';



$lang['$platform=$this->Appconfig->get("ecommerce_platform");'] = 'if ($ platform == "woocommerce")';
$lang['config_default_revenue_account_for_item'] = 'Standarderlöskonto für Artikel';
$lang['config_default_asset_account_for_item'] = 'Standardkonto für Artikel';
$lang['config_default_expense_account_for_item'] = 'Standardkostenkonto für Artikel';
$lang['config_export_expenses_to_quickbooks'] = 'Exportkosten für Quickbooks';
$lang['config_chart_of_accounts'] = 'Quickbooks Kontenplan';
$lang['config_keyword_chart_of_account'] = 'Quickbooks Kontenplan';
$lang['config_default_refund_cash_account_name'] = 'Rückerstattung Cash-Konto';
$lang['config_default_refund_credit_account_name'] = 'Guthabenkonto erstatten';
$lang['config_default_refund_debit_card_account_name'] = 'Debitkartenkonto erstatten';
$lang['config_default_refund_credit_card_account_name'] = 'Kreditkartenkonto erstatten';
$lang['config_default_refund_check_account_name'] = 'Rückerstattung Scheckkonto';
$lang['config_default_refund_deposit_account_name'] = 'Rückzahlungskonto';
$lang['config_default_expense_account_name'] = 'Aufwandskonto';
$lang['config_default_expense_bank_credit_account_name'] = 'Spesenbank / Guthabenkonto';
$lang['config_default_commission_credit_account_name'] = 'Provisionsguthabenkonto';
$lang['config_default_commission_debit_account_name'] = 'Debitkonto der Kommission';
$lang['config_default_house_account_name'] = 'Store-Kontoname';
$lang['config_default_discount_item_name'] = 'Rabattartikel';
$lang['config_default_house_item_name'] = 'Name des Hauselements';
$lang['config_default_store_account_item_name'] = 'Kontoelement speichern';
$lang['config_default_house_account_category_name'] = 'Hauskonto-Kategorie';
$lang['config_default_customer_id'] = 'Standardkundenname';
$lang['config_revenue_id'] = 'Konfiguration konnte nicht gespeichert werden. Standarderlöskonto für Artikel fehlt.';
$lang['config_asset_id'] = 'Konfiguration konnte nicht gespeichert werden. Standard Asset Account für Artikel fehlt';
$lang['config_export_confirm_box_text'] = 'Möchten Sie Artikel in Quickbooks exportieren?';
$lang['config_discount_accounting_id'] = 'Die Abrechnungs-ID für Rabattartikel fehlt für den Verkauf';
$lang['config_sync_for_discount_accounting_id'] = 'Bitte synchronisieren Sie die Artikel vor dem Erstellen von Rechnungen mit Rabatt';


$lang['config_hide_desc_emailed_receipts'] = 'Beschreibung auf E-Mail-Quittungen ausblenden';


$lang['config_default_tax'] = 'Standardsteuer';
$lang['config_default_store_account_tax'] = 'Standardgeschäftskontosteuer';
$lang['config_check_tax_name'] = 'Der angegebene Steuername ist nicht korrekt. Bitte überprüfen Sie die Verkaufs-ID:';
$lang['config_qb_start_sync_date'] = 'Synchronisationsdatum starten';
$lang['config_default_tax_id'] = 'Standardsteuer';
$lang['config_markup_markdown'] = 'Markup / Markdown';
$lang['config_show_total_discount_on_receipt'] = 'Gesamtrabatt bei Erhalt anzeigen';
$lang['config_enable_pdf_receipts'] = 'Aktivieren Sie PDF-Quittungen';
$lang['config_default_credit_limit'] = 'Standard-Kreditlimit';

$lang['config_hide_expire_date_on_barcodes'] = 'Auslaufdatum bei Barcodes ausblenden';

$lang['config_auto_capture_signature'] = 'Auto Capture-Signatur';


$lang['config_pdf_receipt_message'] = 'PDF-Empfangsnachricht im E-Mail-Text';

$lang['config_hide_merchant_id_from_receipt'] = 'Händler-ID vor Quittung ausblenden';


$lang['config_hide_all_prices_on_recv'] = 'ALLE Preise beim Empfang ausblenden';
$lang['config_do_not_delete_serial_number_when_selling'] = 'Löschen Sie die Seriennummer NICHT beim Verkauf';
$lang['config_webhooks'] = 'Web-Hooks';
$lang['config_new_customer_web_hook'] = 'Neue Kunden-Hook-URL';
$lang['config_new_sale_web_hook'] = 'Neue Web Hook-URL';
$lang['config_new_receiving_web_hook'] = 'Neuer Empfangs-Web-Hook';

$lang['config_strict_age_format_check'] = 'Altersüberprüfung strenges Datumsformat';

$lang['config_flat_discounts_discount_tax'] = 'Flat Discount auch Rabatte Steuer';
$lang['config_show_item_kit_items_on_receipt'] = 'Artikel-Kit-Elemente beim Empfang anzeigen';
$lang['config_amount_of_cash_to_be_left_in_drawer_at_closing'] = 'Bargeldbetrag, der bei Schließung noch in der Schublade verbleibt';
$lang['config_hide_tier_on_receipt'] = 'Status bei Erhalt ausblenden';
$lang['config_second_language'] = 'Zweite Sprache für Quittungen';
$lang['config_disable_gift_cards_sold_from_loyalty'] = 'Deaktivieren Sie Geschenkkarten, die aus Treue verdient wurden';
$lang['config_track_shipping_cost_for_receivings'] = 'Verfolgen Sie die Versandkosten für den Empfang';
$lang['config_enable_points_for_giftcard_payments'] = 'Punkte für Geschenkkartenzahlungen aktivieren';




$lang['config_enable_tips'] = 'Tipps aktivieren';

$lang['config_support_regex'] = 'Unterstützt reguläre Ausdrücke. Beispiel: 144. * passt zu allem, was mit 144 beginnt';

$lang['config_not_all_processors_support_tips'] = 'Nicht alle Prozessoren unterstützen die integrierte Spitzenverarbeitung';
$lang['config_require_supplier_recv'] = 'Zulieferer für den Empfang anfordern';
$lang['config_default_payment_type_recv'] = 'Standardzahlungsart für Wareneingänge';
$lang['config_taxjar_api_key'] = 'TaxJar-API-Schlüssel (nur USA)';

$lang['config_quick_variation_grid'] = 'Aktivieren Sie die Schnellauswahl für Variationen im Elementraster';


$lang['config_quick_variation_grid'] = 'Schnellauswahl für Variationen';


$lang['config_quick_variation_grid'] = 'Aktivieren Sie die Schnellauswahl im Elementraster für Variationen';



$lang['config_show_full_category_path'] = 'Vollständigen Kategorieweg beim Suchen anzeigen';


$lang['config_do_not_upload_images_to_ecommerce'] = 'Laden Sie KEINE Bilder in E-Commerce hoch';

$lang['config_woo_enable_html_desc'] = 'Aktivieren Sie HTML für Beschreibungen';

$lang['config_use_rtl_barcode_library'] = 'Verwenden Sie die RTL-Barcode-Bibliothek';
$lang['config_default_new_customer_to_current_location'] = 'Standardmäßig neuer Kunde am aktuellen Standort';
$lang['config_week_start_day'] = 'Wochentag starten';
$lang['config_scan_and_set_sales'] = 'Wählen Sie Menge nach Artikel im Verkauf hinzufügen';
$lang['config_scan_and_set_recv'] = 'Wählen Sie Menge nach Hinzufügen einer Position in den Wareneingängen';
$lang['config_edit_sale_web_hook'] = 'Bearbeiten Sie die Web Hook URL für den Verkauf';
$lang['config_edit_recv_web_hook'] = 'Bearbeiten Sie die empfangende Web Hook-URL';
$lang['config_hide_expire_dashboard'] = 'Auslaufende Elemente im Dashboard ausblenden';
$lang['config_hide_images_in_grid'] = 'Bilder im Raster ausblenden';
$lang['config_taxes_summary_on_receipt'] = 'Steuerpflichtige und nicht steuerpflichtige Zusammenfassung bei Erhalt anzeigen';
$lang['config_collapse_sales_ui_by_default'] = 'Verkaufsschnittstelle standardmäßig reduzieren';
$lang['config_collapse_recv_ui_by_default'] = 'Empfangsschnittstelle standardmäßig reduzieren';
$lang['config_enable_customer_quick_add'] = 'Kunden-Quick-Add aktivieren';
$lang['config_uppercase_receipts'] = 'Quittungstext in Großbuchstaben';

$lang['config_edit_customer_web_hook'] = 'Bearbeiten Sie die Web-Hook-URL des Kunden';
$lang['config_show_selling_price_on_recv'] = 'Verkaufspreis bei Erhalt der Quittung anzeigen';

$lang['config_hide_email_on_receipts'] = 'E-Mail bei Erhalt ausblenden';



$lang['config_hide_available_giftcards'] = 'Verstecke verfügbare Geschenkkarten im Verkaufsregister';


$lang['config_enable_supplier_quick_add'] = 'Schnellzugriff für Lieferanten aktivieren';
$lang['config_sync_inventory_from_location'] = 'Inventar vom Standort aus synchronisieren';
$lang['config_taxes_summary_details_on_receipt'] = 'Steuerdetails beim Empfang anzeigen';
$lang['config_disable_recv_number_on_barcode'] = 'Empfangsnummer auf Barcode deaktivieren';
$lang['config_tax_jar_location'] = 'Verwenden Sie die TaxJar-Standort-API, um Steuern abzurufen';
$lang['config_disable_loyalty_by_default'] = 'Deaktivieren Sie die Loyalität standardmäßig';

$lang['config_ecommerce_only_sync_completed_orders'] = 'Nur abgeschlossene E-Commerce-Bestellungen synchronisieren';

$lang['config_damaged_reasons'] = 'Beschädigte Gründe';

$lang['config_display_item_name_first_for_variation_name'] = 'Zeigen Sie den Artikelnamen zuerst für Variationen von Barcodes an';


$lang['config_do_not_allow_sales_with_zero_value'] = 'Lassen Sie keine Verkäufe mit dem Wert Null zu';

$lang['config_dont_recalculate_cost_price_when_unsuspending_estimates'] = 'Berechnen Sie den Selbstkostenpreis nicht neu, wenn Sie keine Schätzungen vornehmen';


$lang['config_show_signature_on_receiving_receipt'] = 'Zeigen Sie die Unterschrift bei Erhalt der Quittung';

$lang['config_do_not_treat_service_items_as_virtual'] = 'Behandeln Sie Serviceartikel NICHT als virtuelle Produkte im Woo Commerce';

$lang['config_hide_latest_updates_in_header'] = 'Letzte Updates im Header ausblenden';
$lang['config_prompt_amount_for_cash_sale'] = 'Sofortiger Betrag für Barverkauf';
$lang['config_do_not_allow_items_to_go_out_of_stock_when_transfering'] = 'Lassen Sie nicht zu, dass Artikel beim Übertragen nicht vorrätig sind';
$lang['config_show_tags_on_fulfillment_sheet'] = 'Artikel-Tags auf Erfüllungsblatt anzeigen';
$lang['config_automatically_sms_receipt'] = 'Automatischer SMS-Empfang';
$lang['config_items_per_search_suggestions'] = 'Anzahl der Elemente für Suchvorschläge';

$lang['config_shopify_settings_info'] = 'Shopify-Einstellungen';
$lang['config_shopify_shop'] = 'Shopify Store URL';
$lang['config_connect_to_shopify'] = 'Mit Shopify verbinden';
$lang['config_connect_to_shopify_reconnect'] = 'Zum Shopify erneut verbinden';
$lang['config_connected_to_shopify'] = 'Sie sind mit Shopify verbunden';
$lang['config_disconnect_to_shopify'] = 'Trennen Sie die Verbindung zu Shopify';

$lang['config_offline_mode'] = 'Aktivieren Sie den Offline-Modus';
$lang['config_reset_offline_data'] = 'Offline-Daten zurücksetzen';



$lang['config_remove_quantity_suspending'] = 'Menge beim Anhalten entfernen';
$lang['config_auto_sync_offline_sales'] = 'Offline-Verkauf automatisch synchronisieren, wenn Sie wieder online sind';

$lang['config_shopify_billing_terms'] = 'Abrechnung aktivieren - 14-Tage-Testversion, dann {SHOPIFY_PRICE} pro Monat';
$lang['config_shopfiy_billing_failed'] = 'Shopify-Abrechnung fehlgeschlagen';
$lang['config_cancel_shopify'] = 'Shopify-Abrechnung abbrechen';
$lang['config_confirm_cancel_shopify'] = 'Möchten Sie shopify wirklich stornieren?';
$lang['config_step_1'] = 'Schritt 1';
$lang['config_step_2'] = 'Schritt 2';
$lang['config_step_3'] = 'Schritt 3';
$lang['config_step_4'] = 'Schritt 4';
$lang['config_install_shopify_app'] = 'Installieren Sie die Shopify-App';
$lang['config_connect_billing'] = 'Abrechnung verbinden';
$lang['config_choose_sync_options'] = 'Wählen Sie Synchronisierungsoptionen';
$lang['config_ecommerce_sync_running'] = 'Die E-Commerce-Synchronisierung wird jetzt im Hintergrund ausgeführt. Sie können den Status in Store Config überprüfen.';
$lang['config_show_total_on_fulfillment'] = 'Gesamtsumme auf Erfüllungsblatt anzeigen';
$lang['config_connect_shopify_in_app_store'] = 'Sie sind nicht mit Shopify verbunden. Sie können im App Store eine Verbindung zu Shopify herstellen';
$lang['config_override_signature_text'] = 'Signaturtext überschreiben';

$lang['config_delivery_color_based_on'] = 'Lieferfarbe basierend auf';
$lang['config_delivery_color_based_on_status'] = 'Status';
$lang['config_delivery_color_based_on_category'] = 'Kategorie';


$lang['config_update_cost_price_on_transfer'] = 'Aktualisieren Sie den Selbstkostenpreis bei Überweisung';



$lang['config_tip_preset_zero'] = 'Tipp voreingestellter Betrag von 0%';



$lang['config_layaway_statement_message'] = 'Layaway Statement Nachricht';


$lang['config_show_supplier_in_item_search_result'] = 'Lieferanten im Ergebnis der Artikelsuche anzeigen';


$lang['config_show_person_id_on_receipt'] = 'Personen-ID nach Erhalt anzeigen';




$lang['config_import_ecommerce_orders_suspended'] = 'Importieren von E-Commerce-Bestellungen ausgesetzt';

$lang['config_show_images_on_receipt'] = 'Bilder nach Erhalt anzeigen';

$lang['config_disabled_fixed_discounts'] = 'Deaktivieren Sie alle festen Rabatte auf der Verkaufsoberfläche';



$lang['config_always_put_last_added_item_on_top_of_cart'] = 'Immer den zuletzt hinzugefügten Artikel oben in den Warenkorb legen';



$lang['config_show_giftcards_even_if_0_balance'] = 'Geschenkkarten anzeigen, auch wenn kein Guthaben vorhanden ist';




$lang['config_scale_5'] = 'Gewicht Eingebetteter Barcode';



$lang['config_disable_modules'] = 'Module deaktivieren';

$lang['config_hide_description_on_suspended_sales'] = 'Artikelbeschreibung bei ausgesetzten Verkäufen ausblenden';
$lang['config_override_symbol_non_taxable'] = 'Überschreibungssymbol für nicht steuerpflichtig';



$lang['config_hide_categories_sales_grid'] = 'Kategorien im Verkaufsraster ausblenden';
$lang['config_hide_tags_sales_grid'] = 'Tags im Verkaufsraster ausblenden';
$lang['config_hide_favorites_sales_grid'] = 'Favoriten im Verkaufsraster ausblenden';
$lang['config_hide_categories_receivings_grid'] = 'Kategorien im Empfangsraster ausblenden';
$lang['config_hide_tags_receivings_grid'] = 'Tags im Eingangsraster ausblenden';
$lang['config_hide_suppliers_receivings_grid'] = 'Lieferanten im Eingangsraster ausblenden';
$lang['config_hide_favorites_receivings_grid'] = 'Favoriten im Empfangsraster ausblenden';
$lang['config_hide_suppliers_sales_grid'] = 'Lieferanten im Verkaufsraster ausblenden';

$lang['config_offline_mode_sync_period'] = 'Offline-Modus-Synchronisierungszykluszeit (Stunde)';



$lang['config_receipt_download_filename_prefix'] = 'Dateinamenspräfix für Quittung herunterladen';
$lang['config_remove_employee_lastname_from_receipt'] = 'Entfernen Sie den Nachnamen des Mitarbeiters aus der Quittung';


$lang['config_override_symbol_taxes_summary_on_receipt'] = 'AUFHEBUNGSSYMBOL FÜR STEUERBARE UND NICHT STEUERBARE ZUSAMMENFASSUNG AUF DEM EMPFANG';

$lang['config_show_images_on_receipt_width_percent'] = 'Maximale Breite von Artikelbildern beim Empfang (Prozentsatz)';




$lang['hide_supplier_in_item_search_result'] = 'Lieferanten im Ergebnis der Artikelsuche ausblenden';

$lang['config_link_to_receipt'] = 'Link zum Beleg';
$lang['config_sale_summary_info'] = 'Verkaufszusammenfassung Info';
$lang['config_qr_code_format'] = 'QR-Code-Format';

$lang['config_link_to_receipt'] = 'Link zum Beleg';
$lang['config_sale_summary_info'] = 'Verkaufszusammenfassung Info';
$lang['config_qr_code_format'] = 'QR-Code-Format';

$lang['config_override_symbol_taxable_summary'] = 'Symbol für steuerpflichtige Zusammenfassung beim Empfang überschreiben';
$lang['config_override_symbol_non_taxable_summary'] = 'Symbol für nicht steuerpflichtige Zusammenfassung beim Empfang überschreiben';
$lang['config_allow_drag_drop_recv'] = 'Neuordnung auf dem Empfangsbildschirm zulassen';
$lang['config_allow_drag_drop_sale'] = 'Nachbestellung auf dem Verkaufsbildschirm zulassen';
$lang['config_disable_signature_capture_on_terminal_for_phppos_credit_card_processing'] = 'Signaturerfassung für die Verarbeitung von PHP-POS-Kreditkarten deaktivieren';


$lang['config_capture_internal_notes_during_receiving'] = 'Erfassen Sie interne Notizen während des Empfangs';


$lang['config_default_employee_for_deliveries'] = 'Ausfallmitarbeiter für Lieferungen';

$lang['config_disable_verification_for_qr_codes'] = 'Verifizierung für QR-Codes deaktivieren (keine Telefonnummer oder E-Mail erforderlich)';
$lang['config_disable_variation_popup_in_receivings'] = 'Varianten-Popup beim Empfangen deaktivieren';

$lang['config_saudi_arabia_digital_receipt'] = 'Digitale Quittung für Saudi-Arabien';

$lang['config_scale_6'] = 'Gewicht Eingebetteter Barcode EAN 13';

$lang['config_hide_location_name_on_receipt'] = 'Standortnamen auf Quittung ausblenden';
$lang['config_disable_discount_by_percentage'] = 'Deaktivieren Sie ganze prozentuale Rabatte auf der Verkaufsoberfläche';

$lang['Delivery ID'] = 'Liefer-ID';
$lang['config_scale_7'] = 'Gewicht Eingebettete Barcodes EAN 13 4 Preisstellen';

$lang['config_use_main_image_as_default_image_in_e_commerce'] = 'Verwenden Sie das Hauptbild als Standardbild im E-Commerce';


$lang['config_disable_discounts_percentage_per_line_item'] = 'Deaktivieren Sie den Rabattprozentsatz pro Einzelposten';

$lang['config_create_invoices_for_customer_store_account_charges'] = 'Erstellen Sie Rechnungen für Kundenkontogebühren';
$lang['config_create_invoices_for_supplier_store_account_charges'] = 'Erstellen Sie Rechnungen für Kontobelastungen des Lieferantengeschäfts';




$lang['config_turn_on_review_requests'] = 'Aktivieren Sie Überprüfungsanfragen';

$lang['config_additional_appointment_note'] = 'Zusätzliche Terminnotiz';


$lang['config_hover_to_expand_sub_modules'] = 'Bewegen Sie den Mauszeiger, um Untermodule zu erweitern';
$lang['common_parties'] = 'Parteien';


$lang['config_send_sms_via_whatsapp'] = 'Senden Sie SMS über WhatsApp';

$lang['config_additional_appointment_note'] = 'Zusätzliche Terminnotiz';

$lang['config_keywords_help_text'] = 'Hier können Sie einige Schlüsselwörter verwenden.';

$lang['config_keywords_help_text'] = 'Hier können Sie einige Schlüsselwörter verwenden.';





$lang['config_keyword_modules'] = 'Module';

$lang['config_allow_employees_to_use_2fa'] = 'Mitarbeitern die Verwendung von 2FA (Zwei-Faktor-Authentifizierung) erlauben';


$lang['shopify_private_key'] = 'Privater Shopify-Schlüssel';
$lang['shopify_public_key'] = 'Öffentlicher Shopify-Schlüssel';
$lang['config_reconnect_to_shopify'] = 'Verbinden Sie sich erneut mit Shopify';


$lang['config_number_of_decimals_displayed_on_sales_interface'] = 'Anzahl der Dezimalstellen, die auf der Verkaufsoberfläche angezeigt werden';


$lang['shopify_private_key'] = 'Privater Shopify-Schlüssel';
$lang['shopify_public_key'] = 'Öffentlicher Shopify-Schlüssel';
$lang['config_reconnect_to_shopify'] = 'Verbinden Sie sich erneut mit Shopify';



$lang['shopify_private_key'] = 'Privater Shopify-Schlüssel';
$lang['shopify_public_key'] = 'Öffentlicher Shopify-Schlüssel';
$lang['config_reconnect_to_shopify'] = 'Verbinden Sie sich erneut mit Shopify';


$lang['config_keywords_help_text'] = 'Hier können Sie einige Schlüsselwörter verwenden.';

$lang['config_easy_item_clone_button'] = 'Einfache Schaltfläche zum Klonen von Gegenständen';

$lang['config_customer_allow_partial_match'] = 'Teilweise Übereinstimmung für Kundensuche zulassen';

$lang['config_ig_api_bearer_token'] = 'Beschädigter Gadgets-API-Trägertoken';
$lang['config_ig_integration'] = 'Verletzte Gadgets-Integration';
$lang['config_wgp_integration_pkey'] = 'WGP-Integrationspartnerschlüssel';
$lang['config_lookup_api_integration'] = 'Lookup-API-Integration';
$lang['config_wgp_integration_userid'] = 'Benutzer-ID der WGP-Integration';
$lang['config_work_order'] = 'Arbeitsauftrag';
$lang['config_work_order_notes_internal'] = 'Notizen standardmäßig intern';
$lang['config_enable_quick_customers'] = 'Schnelles Hinzufügen/Aktualisieren von Kunden aktivieren';
$lang['config_enable_quick_suppliers'] = 'Schnelles Hinzufügen/Aktualisieren von Lieferanten aktivieren';
$lang['config_enable_quick_items'] = 'Schnelles Hinzufügen/Aktualisieren von Artikeln aktivieren';
$lang['config_enable_quick_expense'] = 'Schnelles Hinzufügen/Aktualisieren von Ausgaben aktivieren';
$lang['config_hide_supplier_on_sales_interface'] = 'Lieferanten auf der Verkaufsoberfläche ausblenden';
$lang['config_hide_supplier_on_recv_interface'] = 'Lieferanten in der Wareneingangsschnittstelle ausblenden';
$lang['config_hide_supplier_from_item_popup'] = 'Lieferanten aus Artikel-Popup ausblenden';
$lang['config_enable_ig_integration'] = 'Aktivieren Sie die Integration beschädigter Gadgets';
$lang['config_enable_wgp_integration'] = 'Aktivieren Sie die Integration von Großhandels-Gadget-Teilen';


$lang['config_sso_info'] = 'Informationen zur einmaligen Anmeldung (SSO).';
$lang['config_sso_protocol'] = 'SSO-Protokoll';
$lang['config_saml'] = 'SAML';
$lang['config_oidc'] = 'OIDC';
$lang['config_saml_idp_entity_id'] = 'Entitäts-ID des Identitätsanbieters (Metadaten)';
$lang['config_saml_single_sign_on_service'] = 'Single-Sign-On-Service-URL';
$lang['config_saml_single_logout_service'] = 'Single-Logout-Service-URL';
$lang['config_saml_x509_cert'] = 'x509-Zertifikat';
$lang['saml_idp_entity_id'] = 'Entitäts-ID des Identitätsanbieters (Metadaten)';
$lang['config_saml_name_id_format'] = 'Saml-Namens-ID-Format';
$lang['config_saml_first_name_field'] = 'Vorname Feldname';
$lang['config_saml_last_name_field'] = 'Nachname Feldname';
$lang['config_saml_email_field'] = 'E-Mail-Feldname';
$lang['config_oidc_host'] = 'OIDC-Host';
$lang['config_oidc_client_id'] = 'OIDC-Client-ID';
$lang['config_oidc_secret'] = 'OIDC-Geheimnis';
$lang['config_oidc_cert_url'] = 'URL des OIDC-Zertifikats';
$lang['config_saml_groups_field'] = 'SAML-Gruppenfeldname';
$lang['config_oidc_groups_field'] = 'OIDC-Gruppenfeld';
$lang['config_oidc_additional_scopes'] = 'OIDC Zusätzliche Geltungsbereiche';
$lang['config_oidc_locations_field'] = 'OIDC-Standortfeld';
$lang['config_saml_locations_field'] = 'SAML-Standortfeld';
$lang['config_oidc_username_field'] = 'OIDC-Benutzernamensfeld';
$lang['config_add_ck_editor'] = 'CK-Editor hinzufügen';
$lang['config_add_ck_editor'] = 'Verwenden Sie den HTML-Editor (CK-Editor)';
$lang['config_do_not_allow_edit_of_overall_subtotal'] = 'Bearbeitung der Gesamtzwischensumme nicht zulassen';
$lang['config_only_allow_sso_logins'] = 'Nur SSO-Anmeldungen zulassen';
$lang['config_enable_p4_integration'] = 'Aktivieren Sie die Parts4Cells.com-Integration';
$lang['config_p4_api_bearer_token'] = 'Parts4cells.com-API-Träger-Token';
$lang['config_work_order_note_status'] = 'Status der Arbeitsauftragsnotiz';
$lang['config_work_order_device_locations'] = 'Standorte der Arbeitsauftragsgeräte';




$lang['gmail_api_token_registered'] = 'PHP Point Of Sale ist jetzt berechtigt, E-Mails im Namen Ihres Google Mail-Kontos zu senden. Sie können dieses Fenster schließen.';
$lang['gmail_api_token_removed'] = 'Ihr Token wurde entfernt.';
$lang['gmail_api_authorize'] = 'Autorisieren';
$lang['gmail_api_refresh'] = 'Aktualisierung';
$lang['gmail_api_remove'] = 'Entfernen';
$lang['gmail_api_error'] = 'Fehler';
$lang['gmail_api_success'] = 'Erfolg';
$lang['gmail_api_authorize_require'] = 'Bitte autorisieren Sie das Google-Konto.';


$lang['config_automatically_email_invoice'] = 'Automatische E-Mail-Rechnung';



$lang['config_disable_default_value_for_tracking_number'] = 'Deaktivieren Sie den Standardwert für die Tracking-Nummer';
$lang['config_disable_supplier_selection_on_sales_interface'] = 'Deaktivieren Sie die Lieferantenauswahl auf der Verkaufsoberfläche';

$lang['config_allow_reorder_sales_receipt'] = 'Nachbestellung auf Verkaufsbeleg zulassen';
$lang['config_allow_reorder_receiving_receipt'] = 'Neubestellung bei Erhalt der Quittung zulassen';
$lang['config_only_allow_current_location_customers'] = 'Erlauben Sie nur Mitarbeitern, Kunden an ihrem Standort festzulegen';
$lang['config_only_allow_current_location_employees'] = 'Erlauben Sie Mitarbeitern nur, Mitarbeiter mit Standort zu sehen';
$lang['config_use_sa_einvoice'] = 'Verwenden Sie SA e-Invoicing';
$lang['config_download_sdk_desc_fill_csr'] = 'Laden Sie das SDK (Fatoora) herunter und installieren Sie es, um das SA E-Invoicing-System zu verwenden. Und füllen Sie die CSR-Eingaben unten aus.';
$lang['config_saudi_tax_common_name_placeholder'] = 'Name oder Bestandsverfolgungsnummer für die Lösungseinheit';
$lang['config_saudi_tax_sn'] = 'EGS-Seriennummer';
$lang['config_saudi_tax_sn_placeholder'] = 'Name des Herstellers oder Lösungsanbieters, Modell oder Version und Seriennummer';
$lang['config_saudi_tax_org_id'] = 'Organisationskennung';
$lang['config_saudi_tax_org_unit_name'] = 'Name der Organisationseinheit';
$lang['config_saudi_tax_org_name'] = 'Organisationsname';
$lang['config_saudi_tax_payer_name'] = 'Name des Steuerzahlers';
$lang['config_saudi_tax_country_name'] = 'Ländername';
$lang['config_saudi_tax_invoice_type_placeholder'] = 'Rechnungsart, TSCZ z. B.: 1111';
$lang['config_saudi_tax_invoice_type'] = 'Rechnungsart';
$lang['config_saudi_tax_location_placeholder'] = 'Standort der Niederlassung oder des Geräts oder der Lösungseinheit. Vorzugsweise die %00Short Address%00 von Saudi National Address';
$lang['config_saudi_tax_industry'] = 'Industrie';
$lang['config_saudi_tax_industry_placeholder'] = 'Branche oder Standort';
$lang['config_saudi_tax_seller_id'] = 'Verkäufer-ID';
$lang['config_saudi_tax_seller_tax_id'] = 'Umsatzsteuer-Identifikationsnummer des Verkäufers';
$lang['config_saudi_tax_seller_scheme_id'] = 'Verkäuferschema-ID';
$lang['config_saudi_tax_seller_scheme_id_select'] = 'Bitte Schema-ID auswählen';
$lang['config_saudi_tax_postal_street_name'] = 'Name der Poststraße';
$lang['config_saudi_tax_postal_building_number'] = 'Postgebäudenummer';
$lang['config_saudi_tax_postal_code'] = 'Postleitzahl';
$lang['config_saudi_tax_postal_code_placeholder'] = 'Muss 5-stellig sein.';
$lang['config_saudi_tax_postal_city_name'] = 'Name der Poststadt';
$lang['config_saudi_tax_postal_district_name'] = 'Name des Postbezirks';
$lang['config_saudi_tax_postal_plot'] = 'Postplot';
$lang['config_saudi_tax_postal_country'] = 'Postland';
$lang['config_saudi_tax_renew'] = 'Erneuern';
$lang['config_saudi_tax_generate'] = 'Generieren';

$lang['config_hide_repair_items_in_sales_interface'] = 'Reparaturartikel in der Verkaufsoberfläche ausblenden';
$lang['config_hide_repair_items_on_receipt'] = 'Reparaturartikel bei Erhalt ausblenden';
$lang['config_disable_name_prefix'] = 'Namenspräfix deaktivieren';
$lang['config_update_base_cost_price_from_units'] = 'Aktualisieren Sie die Basiskosten aus Variationen der Stückzahl';


$lang['config_enable_name_prefix'] = 'Namenspräfix aktivieren';


$lang['config_default_tech_is_logged_employee'] = 'Der Standardtechniker ist angemeldet';
$lang['config_default_workorder_tech_is_logged_employee'] = 'Der Standard-Arbeitsauftragstechniker ist angemeldet';

$lang['config_create_work_order_for_customer'] = 'Arbeitsauftrag für Kunden erstellen';


$lang['config_work_repair_item_taxable'] = 'Reparaturartikel steuerpflichtig';

$lang['config_override_estimate_name'] = 'Schätzungsname überschreiben';
$lang['config_override_employee_label_on_receipt'] = 'Mitarbeiteretikett bei Empfang überschreiben';
$lang['config_remove_weight_from_receipt'] = 'Gewicht vom Beleg entfernen';
$lang['config_show_item_description_service_tag'] = 'Artikelbeschreibung Service-Tag anzeigen';
$lang['config_show_phone_number_service_tag'] = 'Service-Tag für Telefonnummer anzeigen';
$lang['config_change_work_order_status_from_sales'] = 'Arbeitsauftragsstatus von Verkauf ändern';
$lang['config_work_order_change_status_on_sales_complete'] = 'Arbeitsauftragsstatus bei abgeschlossenem Verkauf ändern';
$lang['config_do_not_change'] = 'Verändere dich nicht';
$lang['config_create_work_order_is_checked_by_default_for_sale'] = 'Arbeitsauftrag erstellen ist standardmäßig für den Verkauf aktiviert';

$lang['config_remove_tax_percent_on_receipt'] = 'Steuerprozentsatz bei Erhalt entfernen';







$lang['config_vidapay_info'] = 'VIDAPAY-Einstellungen';
$lang['config_update_vidapay_catalog_for_existing_items'] = 'Aktualisieren Sie den VIDAPAY-Katalog für vorhandene Artikel';

$lang['config_work_order_warranty_checked_product_price_zero'] = 'Produktpreis Null, wenn Garantie geprüft';



$lang['config_show_custom_fields_service_tag_work_orders'] = 'Benutzerdefinierte Felder auf Service-Tag anzeigen';
$lang['config_show_custom_fields_label_service_tag_work_orders'] = 'Beschriftung der benutzerdefinierten Felder auf der Service-Tag-Nummer anzeigen';
$lang['config_show_estimated_repair_date_on_service_tag_work_orders'] = 'Zeigen Sie das geschätzte Reparaturdatum auf dem Service-Tag an';
$lang['config_change_to_recv_when_unsuspending_po'] = 'Wechseln Sie in den Empfangsmodus, wenn Sie die Bestellung aussetzen';
$lang['config_scale_8'] = 'Gewicht Eingebetteter Barcode EAN 13 4 Gewichtsziffern';
$lang['config_dont_show_images_in_search_suggestions'] = 'Zeigen Sie KEINE Bilder in Suchvorschlägen an';


$lang['config_new_item_web_hook'] = 'Neuer Web-Hook';
$lang['config_edit_item_web_hook'] = 'Element-Web-Hook bearbeiten';
$lang['config_edit_work_order_web_hook'] = 'Web-Hook für Arbeitsaufträge bearbeiten';
$lang['config_new_work_order_web_hook'] = 'Neuer Web-Hook für Arbeitsaufträge';


$lang['config_work_orders_show_condensed_receipt'] = 'Kurzbeleg für Arbeitsaufträge anzeigen';



$lang['config_square_terminal_get_id'] = 'Holen Sie sich den Square-Gerätecode';
$lang['config_device_id'] = 'Quadratische Geräte-ID';

$lang['config_work_orders_show_condensed_receipt'] = 'Kurzbeleg für Arbeitsaufträge anzeigen';

$lang['config_square_terminal_delete_id'] = 'Terminal abmelden';

$lang['config_connect_to_woocommerce'] = 'Stellen Sie eine Verbindung zu Woocommerce her';
$lang['config_woocommerce_oauth_set_alert'] = 'Wir aktualisieren die Woo-Commerce-Synchronisierung auf Echtzeit. Damit Woo Commerce weiterhin funktioniert, muss es neu gestartet werden. Klicken Sie zum Einrichten unten.';

$lang['config_prompt_for_sale_id_on_return'] = 'Fordern Sie bei der Rückgabe den Verkaufsausweis an';

$lang['delivery_url_https_error'] = 'Die Delivery_URL verwendet kein HTTPS.';

$lang['config_do_not_allow_sales_with_zero_value_line_items'] = 'Erlauben Sie keine Werbebuchungen mit dem Wert Null';

$lang['config_return_reasons'] = 'Rückgabegründe';
$lang['config_require_customer_for_return'] = 'Kunden zur Rücksendung auffordern';
$lang['config_require_receipt_for_return'] = 'Für die Rücksendung ist eine Quittung erforderlich';


$lang['config_shopifycommerce_oauth_set_alert'] = 'Wir aktualisieren die Shopify-Synchronisierung auf Echtzeit. Damit Shopify weiterhin funktioniert, muss es neu gestartet werden. Klicken Sie zum Einrichten unten';

$lang['config_show_total_at_top_on_receipt'] = 'Gesamtsumme oben auf dem Beleg anzeigen';


$lang['config_ecommerce_realtime'] = 'Echtzeit-Synchronisierung';
$lang['config_dont_lock_suspended_sales'] = 'Sperren Sie ausgesetzte Verkäufe NICHT, um doppelten Zugriff zu verhindern';
$lang['config_show_exchanged_totals_on_receipt'] = 'Umgetauschte Summen beim Empfang anzeigen';
$lang['config_show_prices_on_work_orders'] = 'Preise auf Arbeitsauftragsblatt anzeigen';
?>