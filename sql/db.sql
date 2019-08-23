CREATE TABLE `app_user` (
  `userId` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `nickname` varchar(128) NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` varchar(1024) NOT NULL DEFAULT '' COMMENT '头像',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `app_user`(`userId`, `nickname`, `avatar`) VALUES (1, 'ming', '');
INSERT INTO `app_user`(`userId`, `nickname`, `avatar`) VALUES (2, 'feng', '');
