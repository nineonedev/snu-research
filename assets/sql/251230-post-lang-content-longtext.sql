-- --------------------------------------------------------
-- 게시글 다국어 content 컬럼을 LONGTEXT로 변경
-- 날짜: 2025-12-30
-- 작업: no_post_langs 테이블의 content 컬럼을 TEXT에서 LONGTEXT로 변경
-- 이유: Summernote 에디터에서 생성된 HTML이 TEXT 크기 제한을 초과할 수 있음
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- no_post_langs 테이블의 content 컬럼을 LONGTEXT로 변경
-- LONGTEXT는 최대 4GB까지 저장 가능 (TEXT는 약 65KB)
ALTER TABLE `no_post_langs` 
MODIFY COLUMN `content` LONGTEXT COMMENT '게시글 내용 (Summernote HTML)';

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

