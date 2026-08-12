 -- We changed the nested query, the `parent_id` index|column is no longer used.
 ALTER TABLE `#__categories` DROP INDEX `jf_parent_id`;
 ALTER TABLE `#__tags` DROP INDEX `jf_parent_id`;