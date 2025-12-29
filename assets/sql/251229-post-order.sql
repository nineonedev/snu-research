-- --------------------------------------------------------
-- 게시판 게시글 정렬 순서 컬럼 추가
-- 날짜: 2025-12-29
-- 작업: no_boards 테이블에 post_order 컬럼 추가
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- no_boards 테이블에 post_order 컬럼 추가
-- 'desc' (최신순, 기본값) 또는 'asc' (오래된순)
ALTER TABLE `no_boards` 
ADD COLUMN `post_order` VARCHAR(10) DEFAULT 'desc' COMMENT '게시글 정렬 순서 (desc: 최신순, asc: 오래된순)' 
AFTER `search_key`;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

