# データベース作成
CREATE DATABASE IF NOT EXISTS `onaona`;

# userテーブル作成
CREATE TABLE `onaona`.`users` (
    `user_id` int(10) NOT NULL AUTO_INCREMENT,
    `sex` text NOT NULL,
    `user_name` text NOT NULL,
    `created_at` timestamp NOT NULL,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `user_id` (`user_id`)
) AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

# フォローフォロワー情報
# 誰（followee）が誰（follow）をフォローしてるか
CREATE TABLE `onaona`.`ff` (
    `ff_id` int(15) NOT NULL AUTO_INCREMENT,
    `follow` int(10) NOT NULL,
    `followee` int(10) NOT NULL,
    `created_at` timestamp NOT NULL,
    PRIMARY KEY (`ff_id`),
    UNIQUE KEY `ff_id` (`ff_id`)
) AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

# ログ情報
CREATE TABLE `onaona`.`log` (
    `log_id` int(17) NOT NULL AUTO_INCREMENT,
    `log_user` int(10) NOT NULL,
    `start_time` timestamp NOT NULL,
    PRIMARY KEY (`log_id`),
    UNIQUE KEY `log_id` (`log_id`)
) AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


# ダミーデータを差し込む
INSERT INTO `onaona`.`users` (`user_id`, `sex`, `user_name`, `created_at`) VALUES
(1, '男', 'ちんちんで語ろう', '1981/01/01 0:00:01'),
(2, '女', 'ちんちんでか太郎', '1991/01/01 0:00:01'),
(3, '女', 'ちんちんDE型老', '2001/01/01 0:00:01'),
(4, '男', '沈沈で方老', '2002/01/01 0:00:01'),
(5, '男', '沈々で騙ろう', '2005/01/01 0:00:01');

INSERT INTO `onaona`.`ff` (`ff_id`, `follow`, `followee`, `created_at`) VALUES
(1, 2, 3, '1981/01/01 0:00:01'),
(2, 1, 2, '1991/01/01 0:00:01'),
(3, 2, 4, '2001/01/01 0:00:01'),
(4, 2, 5, '2002/01/01 0:00:01'),
(5, 5, 1, '2005/01/01 0:00:01');

INSERT INTO `onaona`.`log` (`log_id`, `log_user`, `start_time`) VALUES
(1, 2, '1981/01/01 0:00:01'),
(2, 1, '1991/01/01 0:00:01'),
(3, 2, '2001/01/01 0:00:01'),
(4, 2, '2002/01/01 0:00:01'),
(5, 5, '2005/01/01 0:00:01');
