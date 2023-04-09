### Borrar tablas

```
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `order_product`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `clients`;
DROP TABLE IF EXISTS `product_others`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `providers`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `migrations`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `tables`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;
```
