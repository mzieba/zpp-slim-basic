CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `user_surname` varchar(30) NOT NULL,
  `user_city` varchar(30) NOT NULL,
  `user_birthdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `user`
    CHANGE `user_id` `user_id` INT(11) NOT NULL AUTO_INCREMENT,
    ADD PRIMARY KEY (`user_id`);
