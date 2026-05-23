-- Migration: Thêm trường video_url vào bảng recipes
-- Ngày: 2025-01-10

ALTER TABLE `recipes` ADD COLUMN `video_url` VARCHAR(255) NULL DEFAULT NULL AFTER `image`;

-- Cập nhật một số recipe có sẵn với video mẫu
UPDATE `recipes` SET `video_url` = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' WHERE `id` = 1;
UPDATE `recipes` SET `video_url` = 'https://www.youtube.com/watch?v=jNQXAC9IVRw' WHERE `id` = 2;
UPDATE `recipes` SET `video_url` = 'https://www.youtube.com/watch?v=9bZkp7q19f0' WHERE `id` = 3;

