-- Performance indexes for Render + Aiven MySQL.
-- Safe to import after the main schema and sales migration.

DELIMITER //

CREATE PROCEDURE add_index_if_missing(
    IN table_name_in VARCHAR(64),
    IN index_name_in VARCHAR(64),
    IN index_sql_in TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = table_name_in
          AND INDEX_NAME = index_name_in
    ) THEN
        SET @sql = index_sql_in;
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END//

DELIMITER ;

CALL add_index_if_missing('admin', 'idx_admin_user', 'ALTER TABLE `admin` ADD INDEX `idx_admin_user` (`admin_user`)');

CALL add_index_if_missing('transaksi_barang', 'idx_transaksi_status_pergerakan', 'ALTER TABLE `transaksi_barang` ADD INDEX `idx_transaksi_status_pergerakan` (`status_pergerakan`)');
CALL add_index_if_missing('transaksi_barang', 'idx_transaksi_id_barang', 'ALTER TABLE `transaksi_barang` ADD INDEX `idx_transaksi_id_barang` (`id_barang`)');
CALL add_index_if_missing('transaksi_barang', 'idx_transaksi_tanggal', 'ALTER TABLE `transaksi_barang` ADD INDEX `idx_transaksi_tanggal` (`tanggal_transaksi`)');

CALL add_index_if_missing('master_barang', 'idx_master_barang_id', 'ALTER TABLE `master_barang` ADD INDEX `idx_master_barang_id` (`id_barang`)');

CALL add_index_if_missing('sales', 'idx_sales_sale_date', 'ALTER TABLE `sales` ADD INDEX `idx_sales_sale_date` (`sale_date`)');
CALL add_index_if_missing('sales', 'idx_sales_invoice_number', 'ALTER TABLE `sales` ADD INDEX `idx_sales_invoice_number` (`invoice_number`)');

CALL add_index_if_missing('sale_items', 'idx_sale_items_sale_id', 'ALTER TABLE `sale_items` ADD INDEX `idx_sale_items_sale_id` (`sale_id`)');

DROP PROCEDURE add_index_if_missing;
