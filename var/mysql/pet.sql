SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `pet` ;
CREATE SCHEMA IF NOT EXISTS `pet` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `pet` ;

-- -----------------------------------------------------
-- Table `pet`.`download_formats`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`download_formats` ;

CREATE  TABLE IF NOT EXISTS `pet`.`download_formats` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `mimetype` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`downloads`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`downloads` ;

CREATE  TABLE IF NOT EXISTS `pet`.`downloads` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `download_format_id` INT(11) NOT NULL ,
  `name` VARCHAR(200) NOT NULL ,
  `description` LONGTEXT NULL DEFAULT NULL ,
  `date` DATE NOT NULL ,
  `path` VARCHAR(250) NOT NULL ,
  `size` VARCHAR(50) NOT NULL ,
  `thumb` VARCHAR(100) NULL DEFAULT NULL ,
  `subscriber_only` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `downloads_ibfk_1` (`download_format_id` ASC) ,
  CONSTRAINT `downloads_ibfk_1`
    FOREIGN KEY (`download_format_id` )
    REFERENCES `pet`.`download_formats` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 54
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`product_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`product_types` ;

CREATE  TABLE IF NOT EXISTS `pet`.`product_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `type` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id` (`id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`products` ;

CREATE  TABLE IF NOT EXISTS `pet`.`products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `product_type_id` INT(11) NOT NULL ,
  `sku` VARCHAR(30) NOT NULL ,
  `cost` DECIMAL(5,2) NOT NULL DEFAULT 0 ,
  `image` VARCHAR(100) NULL DEFAULT NULL ,
  `active` INT(1) NOT NULL DEFAULT 1 ,
  `max_qty` INT(2) NULL ,
  `is_giftable` INT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id` (`id` ASC) ,
  UNIQUE INDEX `sku` (`sku` ASC) ,
  INDEX `product_type_id` (`product_type_id` ASC) ,
  CONSTRAINT `products_ibfk_1`
    FOREIGN KEY (`product_type_id` )
    REFERENCES `pet`.`product_types` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 102
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`shipping`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`shipping` ;

CREATE  TABLE IF NOT EXISTS `pet`.`shipping` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `usa` DECIMAL(5,2) NOT NULL DEFAULT '0.00' ,
  `can` DECIMAL(5,2) NOT NULL DEFAULT '0.00' ,
  `intl` DECIMAL(5,2) NOT NULL DEFAULT '0.00' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id` (`id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`subscription_zones`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`subscription_zones` ;

CREATE  TABLE IF NOT EXISTS `pet`.`subscription_zones` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `zone` VARCHAR(10) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id` (`id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`promos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`promos` ;

CREATE  TABLE IF NOT EXISTS `pet`.`promos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(20) NOT NULL ,
  `expiration` DATE NOT NULL ,
  `description` VARCHAR(200) NULL DEFAULT NULL ,
  `public_description` LONGTEXT NULL DEFAULT NULL ,
  `receipt_description` LONGTEXT NULL DEFAULT NULL ,
  `banner` VARCHAR(100) NOT NULL ,
  `discount` DECIMAL(5,2) NOT NULL ,
  `extra_days` INT(11) NOT NULL ,
  `uses` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 119
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`users` ;

CREATE  TABLE IF NOT EXISTS `pet`.`users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(30) NOT NULL ,
  `first_name` VARCHAR(50) NOT NULL ,
  `last_name` VARCHAR(50) NOT NULL ,
  `email` VARCHAR(75) NULL DEFAULT NULL ,
  `password` VARCHAR(128) NOT NULL ,
  `is_staff` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_active` TINYINT(4) NOT NULL DEFAULT '1' ,
  `is_superuser` TINYINT(4) NOT NULL DEFAULT '0' ,
  `last_login` DATETIME NULL ,
  `date_joined` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) ,
  UNIQUE INDEX `email` (`email` ASC) ,
  INDEX `index3` (`username` ASC) ,
  INDEX `index4` (`email` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 87701000
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`orders`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`orders` ;

CREATE  TABLE IF NOT EXISTS `pet`.`orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NULL DEFAULT NULL ,
  `promo_id` INT(11) NULL DEFAULT NULL ,
  `date_created` DATETIME NOT NULL ,
  `date_updated` DATETIME NOT NULL ,
  `email` VARCHAR(75) NOT NULL ,
  `billing_first_name` VARCHAR(100) NOT NULL ,
  `billing_last_name` VARCHAR(100) NOT NULL ,
  `billing_address` VARCHAR(100) NOT NULL ,
  `billing_address_2` VARCHAR(100) NOT NULL ,
  `billing_company` VARCHAR(100) NOT NULL ,
  `billing_city` VARCHAR(100) NOT NULL ,
  `billing_country` VARCHAR(100) NOT NULL ,
  `billing_state` VARCHAR(100) NOT NULL ,
  `billing_postal_code` VARCHAR(100) NOT NULL ,
  `billing_phone` VARCHAR(100) NOT NULL ,
  `shipping_first_name` VARCHAR(100) NOT NULL ,
  `shipping_last_name` VARCHAR(100) NOT NULL ,
  `shipping_address` VARCHAR(100) NOT NULL COMMENT '	' ,
  `shipping_address_2` VARCHAR(100) NOT NULL ,
  `shipping_company` VARCHAR(100) NOT NULL ,
  `shipping_city` VARCHAR(100) NOT NULL ,
  `shipping_state` VARCHAR(100) NOT NULL ,
  `shipping_postal_code` VARCHAR(100) NOT NULL ,
  `shipping_country` VARCHAR(100) NOT NULL ,
  `shipping_phone` VARCHAR(50) NOT NULL ,
  `shipping` DECIMAL(5,2) NOT NULL DEFAULT '0.00' ,
  `discount` DECIMAL(5,2) NOT NULL DEFAULT 0 ,
  `total` DECIMAL(5,2) NOT NULL DEFAULT '0.00' ,
  `phone_order` TINYINT(1) NOT NULL ,
  `active` INT(1) NOT NULL DEFAULT '1' ,
  `email_sent` INT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id` (`id` ASC) ,
  INDEX `promo_id` (`promo_id` ASC) ,
  INDEX `orders_ibfk_2` (`promo_id` ASC) ,
  INDEX `orders_ibfk_3` (`user_id` ASC) ,
  INDEX `email_sent` (`email_sent` ASC) ,
  CONSTRAINT `orders_ibfk_2`
    FOREIGN KEY (`promo_id` )
    REFERENCES `pet`.`promos` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `orders_ibfk_3`
    FOREIGN KEY (`user_id` )
    REFERENCES `pet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1128415
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`ordered_products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`ordered_products` ;

CREATE  TABLE IF NOT EXISTS `pet`.`ordered_products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `order_id` INT(11) NOT NULL ,
  `product_id` INT(11) NOT NULL ,
  `qty` INT(3) NOT NULL DEFAULT '0' ,
  `cost` DECIMAL(5,2) NOT NULL DEFAULT '0.00' ,
  `discount` DECIMAL(5,2) NOT NULL DEFAULT '0.00' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id` (`id` ASC) ,
  INDEX `order_id` (`order_id` ASC) ,
  INDEX `product_id` (`product_id` ASC) ,
  CONSTRAINT `ordered_products_ibfk_1`
    FOREIGN KEY (`order_id` )
    REFERENCES `pet`.`orders` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `ordered_products_ibfk_2`
    FOREIGN KEY (`product_id` )
    REFERENCES `pet`.`products` (`id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
AUTO_INCREMENT = 131071
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`subscriptions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`subscriptions` ;

CREATE  TABLE IF NOT EXISTS `pet`.`subscriptions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `zone_id` INT(11) NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `description` LONGTEXT NULL DEFAULT NULL ,
  `term_months` INT(11) NOT NULL DEFAULT '1' ,
  `is_renewal` INT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id` (`id` ASC) ,
  INDEX `zone_id` (`zone_id` ASC) ,
  CONSTRAINT `subscription_products_ibfk_3`
    FOREIGN KEY (`zone_id` )
    REFERENCES `pet`.`subscription_zones` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 16
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`courses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`courses` ;

CREATE  TABLE IF NOT EXISTS `pet`.`courses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `description` LONGTEXT NULL DEFAULT NULL ,
  `slug` VARCHAR(200) NOT NULL ,
  `active` TINYINT(4) NOT NULL DEFAULT '0' ,
  `free` TINYINT(4) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id` (`id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`physical_products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`physical_products` ;

CREATE  TABLE IF NOT EXISTS `pet`.`physical_products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `product_id` INT(11) NOT NULL ,
  `shipping_id` INT(11) NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `description` LONGTEXT NULL DEFAULT NULL ,
  `sequence` INT(5) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id` (`id` ASC) ,
  UNIQUE INDEX `product_id_UNIQUE` (`product_id` ASC) ,
  INDEX `product_id` (`product_id` ASC) ,
  INDEX `shipping_id` (`shipping_id` ASC) ,
  CONSTRAINT `physical_products_ibfk_1`
    FOREIGN KEY (`product_id` )
    REFERENCES `pet`.`products` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `physical_products_ibfk_2`
    FOREIGN KEY (`shipping_id` )
    REFERENCES `pet`.`shipping` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 16
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`promo_products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`promo_products` ;

CREATE  TABLE IF NOT EXISTS `pet`.`promo_products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `promo_id` INT(11) NOT NULL ,
  `product_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `promo_id` (`promo_id` ASC) ,
  INDEX `product_id` (`product_id` ASC) ,
  INDEX `promo_id_ibfk_1` (`promo_id` ASC) ,
  INDEX `product_id_ibfk_1` (`product_id` ASC) ,
  CONSTRAINT `promo_id_ibfk_1`
    FOREIGN KEY (`promo_id` )
    REFERENCES `pet`.`promos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `product_id_ibfk_1`
    FOREIGN KEY (`product_id` )
    REFERENCES `pet`.`products` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1024
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`payment_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`payment_types` ;

CREATE  TABLE IF NOT EXISTS `pet`.`payment_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `table_name` VARCHAR(30) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`order_payments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`order_payments` ;

CREATE  TABLE IF NOT EXISTS `pet`.`order_payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `order_id` INT(11) NOT NULL ,
  `payment_type_id` INT(11) NOT NULL ,
  `amount` DECIMAL(5,2) NOT NULL DEFAULT '0.00' ,
  `date` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `order_id` (`order_id` ASC) ,
  INDEX `order_payments_ibfk_1` (`order_id` ASC) ,
  INDEX `order_payments_ibfk_2` (`payment_type_id` ASC) ,
  CONSTRAINT `order_payments_ibfk_1`
    FOREIGN KEY (`order_id` )
    REFERENCES `pet`.`orders` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `order_payments_ibfk_2`
    FOREIGN KEY (`payment_type_id` )
    REFERENCES `pet`.`payment_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`order_payments_payflow`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`order_payments_payflow` ;

CREATE  TABLE IF NOT EXISTS `pet`.`order_payments_payflow` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `order_payment_id` INT(11) NOT NULL ,
  `cc_number` VARCHAR(16) NOT NULL ,
  `cc_expiration_month` INT(2) NULL ,
  `cc_expiration_year` INT(2) NULL ,
  `pnref` VARCHAR(12) NOT NULL ,
  `ppref` VARCHAR(17) NOT NULL ,
  `correlationid` VARCHAR(13) NOT NULL ,
  `cvv2match` VARCHAR(1) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `order_payments_payflow_ibfk_1` (`order_payment_id` ASC) ,
  UNIQUE INDEX `order_payment_id_UNIQUE` (`order_payment_id` ASC) ,
  CONSTRAINT `order_payments_payflow_ibfk_1`
    FOREIGN KEY (`order_payment_id` )
    REFERENCES `pet`.`order_payments` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`order_payments_paypal`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`order_payments_paypal` ;

CREATE  TABLE IF NOT EXISTS `pet`.`order_payments_paypal` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `order_payment_id` INT(11) NOT NULL ,
  `correlationid` VARCHAR(20) NOT NULL ,
  `transaction_id` VARCHAR(20) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `order_payments_paypal_ibfk_2` (`order_payment_id` ASC) ,
  UNIQUE INDEX `order_payment_id_UNIQUE` (`order_payment_id` ASC) ,
  CONSTRAINT `order_payments_paypal_ibfk_2`
    FOREIGN KEY (`order_payment_id` )
    REFERENCES `pet`.`order_payments` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`products_downloads`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`products_downloads` ;

CREATE  TABLE IF NOT EXISTS `pet`.`products_downloads` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `product_id` INT(11) NOT NULL ,
  `download_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `product_id_UNIQUE` (`product_id` ASC) ,
  UNIQUE INDEX `download_id_UNIQUE` (`download_id` ASC) ,
  INDEX `products_downloads_fk_1` (`product_id` ASC) ,
  INDEX `products_downloads_fk_2` (`download_id` ASC) ,
  CONSTRAINT `products_downloads_fk_1`
    FOREIGN KEY (`product_id` )
    REFERENCES `pet`.`products` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `products_downloads_fk_2`
    FOREIGN KEY (`download_id` )
    REFERENCES `pet`.`downloads` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 64
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`products_courses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`products_courses` ;

CREATE  TABLE IF NOT EXISTS `pet`.`products_courses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `product_id` INT(11) NOT NULL ,
  `course_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `product_id_UNIQUE` (`product_id` ASC) ,
  UNIQUE INDEX `course_id_UNIQUE` (`course_id` ASC) ,
  INDEX `products_courses_fk_1` (`product_id` ASC) ,
  INDEX `products_courses_fk_2` (`course_id` ASC) ,
  CONSTRAINT `products_courses_fk_1`
    FOREIGN KEY (`product_id` )
    REFERENCES `pet`.`products` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `products_courses_fk_2`
    FOREIGN KEY (`course_id` )
    REFERENCES `pet`.`courses` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`products_subscriptions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`products_subscriptions` ;

CREATE  TABLE IF NOT EXISTS `pet`.`products_subscriptions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `product_id` INT(11) NOT NULL ,
  `subscription_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `product_id_UNIQUE` (`product_id` ASC) ,
  INDEX `products_subscriptions_fk_1` (`product_id` ASC) ,
  INDEX `products_subscriptions_fk_2` (`subscription_id` ASC) ,
  CONSTRAINT `products_subscriptions_fk_1`
    FOREIGN KEY (`product_id` )
    REFERENCES `pet`.`products` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `products_subscriptions_fk_2`
    FOREIGN KEY (`subscription_id` )
    REFERENCES `pet`.`subscriptions` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`user_profiles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`user_profiles` ;

CREATE  TABLE IF NOT EXISTS `pet`.`user_profiles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `billing_address` VARCHAR(128) NOT NULL ,
  `billing_address_2` VARCHAR(50) NOT NULL ,
  `billing_company` VARCHAR(100) NOT NULL ,
  `billing_city` VARCHAR(50) NOT NULL ,
  `billing_state` VARCHAR(30) NOT NULL ,
  `billing_postal_code` VARCHAR(30) NOT NULL ,
  `billing_country` VARCHAR(50) NOT NULL ,
  `billing_phone` VARCHAR(50) NOT NULL ,
  `shipping_first_name` VARCHAR(50) NOT NULL COMMENT '		' ,
  `shipping_last_name` VARCHAR(50) NOT NULL ,
  `shipping_address` VARCHAR(128) NOT NULL ,
  `shipping_address_2` VARCHAR(50) NOT NULL ,
  `shipping_company` VARCHAR(100) NOT NULL ,
  `shipping_city` VARCHAR(50) NOT NULL ,
  `shipping_state` VARCHAR(30) NOT NULL ,
  `shipping_postal_code` VARCHAR(30) NOT NULL ,
  `shipping_country` VARCHAR(50) NOT NULL ,
  `shipping_phone` VARCHAR(50) NOT NULL ,
  `marketing` VARCHAR(100) NULL ,
  `occupation` VARCHAR(100) NULL ,
  `opt_in` TINYINT(1) NOT NULL DEFAULT '0' ,
  `opt_in_partner` TINYINT(1) NOT NULL DEFAULT '0' ,
  `opt_in_subscriber` TINYINT(1) NOT NULL DEFAULT '0' ,
  `comp` TINYINT(1) NOT NULL DEFAULT '0' ,
  `version` VARCHAR(100) NULL ,
  `platform` VARCHAR(100) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `user_profiles_ibfk_1` (`user_id` ASC) ,
  CONSTRAINT `user_profiles_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `pet`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 87701000
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`order_payments_check`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`order_payments_check` ;

CREATE  TABLE IF NOT EXISTS `pet`.`order_payments_check` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `order_payment_id` INT NOT NULL ,
  `check_number` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `order_payment_id_UNIQUE` (`order_payment_id` ASC) ,
  INDEX `order_payments_check_ibfk_1` (`order_payment_id` ASC) ,
  CONSTRAINT `order_payments_check_ibfk_1`
    FOREIGN KEY (`order_payment_id` )
    REFERENCES `pet`.`order_payments` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pet`.`user_password_tokens`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`user_password_tokens` ;

CREATE  TABLE IF NOT EXISTS `pet`.`user_password_tokens` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `token` VARCHAR(100) NOT NULL ,
  `timestamp` DATETIME NOT NULL ,
  `attempts` TINYINT NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `user_password_resets_fk_1` (`user_id` ASC) ,
  UNIQUE INDEX `token_UNIQUE` (`token` ASC) ,
  CONSTRAINT `user_password_resets_fk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `pet`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pet`.`digital_subscriptions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`digital_subscriptions` ;

CREATE  TABLE IF NOT EXISTS `pet`.`digital_subscriptions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `description` LONGTEXT NULL DEFAULT NULL ,
  `is_renewal` INT(1) NOT NULL DEFAULT '0' ,
  `is_recurring` TINYINT(1) NOT NULL DEFAULT 0 ,
  `term_months` INT(4) NULL COMMENT '					' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id` (`id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 16
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`products_digital_subscriptions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`products_digital_subscriptions` ;

CREATE  TABLE IF NOT EXISTS `pet`.`products_digital_subscriptions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `product_id` INT(11) NOT NULL ,
  `digital_subscription_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `product_id_UNIQUE` (`product_id` ASC) ,
  INDEX `products_subscriptions_fk_1` (`product_id` ASC) ,
  INDEX `products_subscriptions_fk_2` (`digital_subscription_id` ASC) ,
  CONSTRAINT `products_digital_subscriptions_fk_1`
    FOREIGN KEY (`product_id` )
    REFERENCES `pet`.`products` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `products_digital_subscriptions_fk_2`
    FOREIGN KEY (`digital_subscription_id` )
    REFERENCES `pet`.`digital_subscriptions` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`order_subscriptions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`order_subscriptions` ;

CREATE  TABLE IF NOT EXISTS `pet`.`order_subscriptions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `order_id` INT(11) NULL ,
  `expiration` DATE NOT NULL ,
  `digital_only` INT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `order_subscriptions_ibfk_1` (`user_id` ASC) ,
  INDEX `order_subscriptions_ibfk_2` (`order_id` ASC) ,
  CONSTRAINT `order_subscriptions_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `pet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `order_subscriptions_ibfk_2`
    FOREIGN KEY (`order_id` )
    REFERENCES `pet`.`orders` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 98303
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pet`.`sessions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pet`.`sessions` ;

CREATE  TABLE IF NOT EXISTS `pet`.`sessions` (
  `id` CHAR(32) NOT NULL ,
  `modified` INT NULL ,
  `lifetime` INT NULL ,
  `data` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Placeholder table for view `pet`.`view_products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pet`.`view_products` (`id` INT, `product_type_id` INT, `sku` INT, `cost` INT, `image` INT, `active` INT, `max_qty` INT, `is_giftable` INT, `name` INT, `description` INT);

-- -----------------------------------------------------
-- View `pet`.`view_products`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `pet`.`view_products` ;
DROP TABLE IF EXISTS `pet`.`view_products`;
USE `pet`;
CREATE  OR REPLACE VIEW `pet`.`view_products` AS
select p.*, d.name, d.description
from products p
left join products_downloads pd
on p.id = pd.product_id
left join downloads d
on pd.download_id = d.id
where p.product_type_id = 1

union
select p.*, pp.name, pp.description
from products p
left join physical_products pp
on p.id = pp.product_id
where p.product_type_id = 2

union
select p.*, c.name, c.description
from products p
left join products_courses pc
on p.id = pc.product_id
left join courses c
on pc.course_id = c.id
where p.product_type_id = 3

union
select p.*, s.name, s.description
from products p
left join products_subscriptions ps
on p.id = ps.product_id
left join subscriptions s
on ps.subscription_id = s.id
where p.product_type_id = 4;
;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
