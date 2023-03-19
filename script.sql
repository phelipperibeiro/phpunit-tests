create database phpunit;
use phpunit;

CREATE TABLE `leiloes`
(
    `id`         int(11)      NOT NULL,
    `descricao`  varchar(255) NOT NULL,
    `dataInicio` date         NOT NULL,
    `finalizado` boolean      NOT NULL
);

ALTER TABLE `leiloes`
    ADD PRIMARY KEY (`id`);
ALTER TABLE `leiloes`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 9;