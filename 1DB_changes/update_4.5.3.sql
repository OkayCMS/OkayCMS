-- Remote image URLs in CSV import exceeded varchar(255) and were truncated silently.
ALTER TABLE `ok_images`
    MODIFY `filename` VARCHAR(1024) NOT NULL DEFAULT '';

ALTER TABLE `ok_spec_img`
    MODIFY `filename` VARCHAR(1024) NOT NULL DEFAULT '';
