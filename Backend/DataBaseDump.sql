-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Mar 31, 2026 at 12:08 PM
-- Server version: 12.0.2-MariaDB-ubu2404
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `developmentdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `shared_by` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `published_year` smallint(5) UNSIGNED NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image_url` varchar(255) DEFAULT NULL,
  `thumbnail_image_url` varchar(255) DEFAULT NULL,
  `book_condition` enum('Unknown','New','Good','Fair','Poor') NOT NULL DEFAULT 'Unknown',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `page_count` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `owner_review` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `shared_by`, `title`, `author`, `isbn`, `published_year`, `genre`, `description`, `cover_image_url`, `thumbnail_image_url`, `book_condition`, `created_at`, `updated_at`, `page_count`, `is_active`, `owner_review`) VALUES
(40, 1, 'Project Hail Mary', 'Andy Weir', '0593135229', 2022, 'Fiction', 'THE #1 NEW YORK TIMES BESTSELLER FROM THE AUTHOR OF THE MARTIAN • Soon to be a major motion picture starring Ryan Gosling, directed by Phil Lord and Christopher Miller, with a screenplay by Drew Goddard A lone astronaut must save the earth from disaster in this “propulsive” (Entertainment Weekly), cinematic thriller full of suspense, humor, and fascinating science. HUGO AWARD FINALIST • ONE OF THE YEAR’S BEST BOOKS: Bill Gates, GatesNotes, New York Public Library, Parade, Newsweek, Polygon, Shelf Awareness, She Reads, Kirkus Reviews, Library Journal • New York Times Readers Pick: 100 Best Books of the 21st Century “An epic story of redemption, discovery and cool speculative sci-fi.”—USA Today “If you loved The Martian, you’ll go crazy for Weir’s latest.”—The Washington Post Ryland Grace is the sole survivor on a desperate, last-chance mission—and if he fails, humanity and the earth itself will perish. Except that right now, he doesn’t know that. He can’t even remember his own name, let alone the nature of his assignment or how to complete it. All he knows is that he’s been asleep for a very, very long time. And he’s just been awakened to find himself millions of miles from home, with nothing but two corpses for company. His crewmates dead, his memories fuzzily returning, Ryland realizes that an impossible task now confronts him. Hurtling through space on this tiny ship, it’s up to him to puzzle out an impossible scientific mystery—and conquer an extinction-level threat to our species. And with the clock ticking down and the nearest human being light-years away, he’s got to do it all alone. Or does he? An irresistible interstellar adventure as only Andy Weir could deliver, Project Hail Mary is a tale of discovery, speculation, and survival to rival The Martian—while taking us to places it never dreamed of going.', 'http://books.google.com/books/content?id=iEiHEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=iEiHEAAAQBAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Fair', '2025-12-29 22:54:44', '2026-01-12 19:46:12', 497, 0, NULL),
(41, 1, 'Alice in Borderland, Vol. 1', 'Haro Aso', '9781974728374', 2022, 'Comics & Graphic Novels', 'An action-packed thriller and source of the hit Netflix drama where the only way to survive is to play the game! Battle Royale: Angel\'s Border; Deadman Wonderland; Death Note; Zom 100 The first game starts with a bang, but Ryohei manages to beat the clock and save his friends. It’s a short-lived victory, however, as they discover that winning only earns them a few days’ grace period. If they want to get home, they’re going to have to start playing a lot harder.', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Fair', '2025-12-29 22:55:41', '2026-01-12 19:47:25', 344, 0, NULL),
(42, 12, 'Jurassic Park', 'Michael Crichton', '0099282917', 1991, 'Fiction', 'On a remote jungle island, genetic engineers have created a dinosaur game park. An astonishing technique for recovering and cloning dinosaur DNA has been discovered. Now one of mankind\'s most thrilling fantasies has come true and the first dinosaurs that the Earth has seen in the time of man emerge. But, as always, there is a dark side to the fantasy and after a catastrophe destroys the park\'s defence systems, the scientists and tourists are left fighting for survival.', 'http://books.google.com/books/content?id=iqfNIpyLHrgC&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=iqfNIpyLHrgC&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Fair', '2025-12-30 00:43:08', '2026-01-12 19:47:25', 416, 0, NULL),
(44, 12, 'Project Hail Mary', 'Andy Weir', '0593135229', 2022, 'Fiction', 'THE #1 NEW YORK TIMES BESTSELLER FROM THE AUTHOR OF THE MARTIAN • Soon to be a major motion picture starring Ryan Gosling, directed by Phil Lord and Christopher Miller, with a screenplay by Drew Goddard A lone astronaut must save the earth from disaster in this “propulsive” (Entertainment Weekly), cinematic thriller full of suspense, humor, and fascinating science. HUGO AWARD FINALIST • ONE OF THE YEAR’S BEST BOOKS: Bill Gates, GatesNotes, New York Public Library, Parade, Newsweek, Polygon, Shelf Awareness, She Reads, Kirkus Reviews, Library Journal • New York Times Readers Pick: 100 Best Books of the 21st Century “An epic story of redemption, discovery and cool speculative sci-fi.”—USA Today “If you loved The Martian, you’ll go crazy for Weir’s latest.”—The Washington Post Ryland Grace is the sole survivor on a desperate, last-chance mission—and if he fails, humanity and the earth itself will perish. Except that right now, he doesn’t know that. He can’t even remember his own name, let alone the nature of his assignment or how to complete it. All he knows is that he’s been asleep for a very, very long time. And he’s just been awakened to find himself millions of miles from home, with nothing but two corpses for company. His crewmates dead, his memories fuzzily returning, Ryland realizes that an impossible task now confronts him. Hurtling through space on this tiny ship, it’s up to him to puzzle out an impossible scientific mystery—and conquer an extinction-level threat to our species. And with the clock ticking down and the nearest human being light-years away, he’s got to do it all alone. Or does he? An irresistible interstellar adventure as only Andy Weir could deliver, Project Hail Mary is a tale of discovery, speculation, and survival to rival The Martian—while taking us to places it never dreamed of going.', 'http://books.google.com/books/content?id=iEiHEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=iEiHEAAAQBAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Fair', '2025-12-30 22:42:46', '2026-01-12 19:47:25', 497, 0, NULL),
(46, 12, 'Sapiens', 'Yuval Noah Harari', '9789400404908', 2020, 'Civilization', 'Honderdduizend jaar geleden waren er wel zes verschillende menssoorten. Nu is er maar één soort over, en dat zijn wij. Homo sapiens. Hoe komt het dat alleen wij zijn overgebleven? Hoe kwamen onze voorvaderen op het idee om steden en zelfs koninkrijken te stichten? Waarom gingen we in goden geloven, maar ook in natiestaten, en in bedrijven. Waarom vertrouwen we op geld, boeken en wetten? En hoe zal onze wereld er in de toekomst uitzien?', '', '', 'Fair', '2025-12-30 23:24:09', '2026-01-12 19:47:25', 0, 0, NULL),
(47, 1, 'Project Hail Mary', 'Andy Weir', '9781529157468', 2022, '', 'AS SEEN ON BARACK OBAMA\'S 2021 SUMMER READING LIST \'THE MOST ENJOYABLE HARD SF I HAVE READ IN YEARS\' THE GUARDIAN OUT NOW from the bestselling author of THE MARTIAN \'Weir\'s finest work to date. . . This is the one book I read last year that I am certain I can recommend to anyone, no matter who, and know they\'ll love it.\' BRANDON SANDERSON \'If you like a lot of science in your science fiction, Andy Weir is the writer for you. . . This one has everything fans of old school SF (like me) love.\' GEORGE R.R. MARTIN \'Brilliantly funny and enjoyable. One of the most plausible science fiction books I\'ve ever read\' TIM PEAKE, astronaut ________________________________________ A lone astronaut. An impossible mission. An ally he never imagined. Ryland Grace is the sole survivor on a desperate, last-chance mission - and if he fails, humanity and the earth itself will perish. Except that right now, he doesn\'t know that. He can\'t even remember his own name, let alone the nature of his assignment or how to complete it. All he knows is that he\'s been asleep for a very, very long time. And he\'s just been awakened to find himself millions of miles from home, with nothing but two corpses for company. His crewmates dead, his memories fuzzily returning, Ryland realizes that an impossible task now confronts him. Hurtling through space on this tiny ship, it\'s up to him to puzzle out an impossible scientific mystery-and conquer an extinction-level threat to our species. And with the clock ticking down and the nearest human being light-years away, he\'s got to do it all alone. Or does he? An irresistible interstellar adventure as only Andy Weir could imagine it, Project Hail Mary is a tale of discovery, speculation, and survival to rival The Martian -- while taking us to places it never dreamed of going. ________________________________________ \'One of the most original, compelling, and fun voyages I\'ve ever taken.\' ERNEST CLINE, author of Ready Player One and Ready Player Two \'Undisputedly the best book I\'ve read in a very, very long time. Mark my words: Project Hail Mary is destined to become a classic.\' BLAKE CROUCH \'Andy Weir\'s brilliant Project Hail Mary...is one of those stirring sci-fi novels about every government on Earth banding together, through science, to save civilisation from collapse. I loved it.\' THE TIMES \'A suspenseful portrait of human ingenuity and resilience [that] builds to an unexpectedly moving ending. A winner.\' PUBLISHERS WEEKLY \'Weir returns with gusto . . . his writing flows naturally, and his characters and dialogue crackle with energy. With this novel, he takes his place as a genuine star in the mainstream SF world.\' BOOKLIST', 'http://books.google.com/books/content?id=eFSjzgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=eFSjzgEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Fair', '2026-01-09 10:52:11', '2026-01-12 19:47:25', 448, 0, NULL),
(48, 12, 'Jurassic Park', 'Michael Crichton', '0099282917', 1991, 'Fiction', 'On a remote jungle island, genetic engineers have created a dinosaur game park. An astonishing technique for recovering and cloning dinosaur DNA has been discovered. Now one of mankind\'s most thrilling fantasies has come true and the first dinosaurs that the Earth has seen in the time of man emerge. But, as always, there is a dark side to the fantasy and after a catastrophe destroys the park\'s defence systems, the scientists and tourists are left fighting for survival.', 'http://books.google.com/books/content?id=iqfNIpyLHrgC&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=iqfNIpyLHrgC&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Fair', '2026-01-09 18:26:47', '2026-01-13 17:26:00', 416, 0, NULL),
(49, 12, 'Project Hail Mary', 'Andy Weir', '0593135229', 2022, 'Fiction', 'THE #1 NEW YORK TIMES BESTSELLER FROM THE AUTHOR OF THE MARTIAN • Soon to be a major motion picture starring Ryan Gosling, directed by Phil Lord and Christopher Miller, with a screenplay by Drew Goddard A lone astronaut must save the earth from disaster in this “propulsive” (Entertainment Weekly), cinematic thriller full of suspense, humor, and fascinating science. HUGO AWARD FINALIST • ONE OF THE YEAR’S BEST BOOKS: Bill Gates, GatesNotes, New York Public Library, Parade, Newsweek, Polygon, Shelf Awareness, She Reads, Kirkus Reviews, Library Journal • New York Times Readers Pick: 100 Best Books of the 21st Century “An epic story of redemption, discovery and cool speculative sci-fi.”—USA Today “If you loved The Martian, you’ll go crazy for Weir’s latest.”—The Washington Post Ryland Grace is the sole survivor on a desperate, last-chance mission—and if he fails, humanity and the earth itself will perish. Except that right now, he doesn’t know that. He can’t even remember his own name, let alone the nature of his assignment or how to complete it. All he knows is that he’s been asleep for a very, very long time. And he’s just been awakened to find himself millions of miles from home, with nothing but two corpses for company. His crewmates dead, his memories fuzzily returning, Ryland realizes that an impossible task now confronts him. Hurtling through space on this tiny ship, it’s up to him to puzzle out an impossible scientific mystery—and conquer an extinction-level threat to our species. And with the clock ticking down and the nearest human being light-years away, he’s got to do it all alone. Or does he? An irresistible interstellar adventure as only Andy Weir could deliver, Project Hail Mary is a tale of discovery, speculation, and survival to rival The Martian—while taking us to places it never dreamed of going.', 'http://books.google.com/books/content?id=iEiHEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=iEiHEAAAQBAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Unknown', '2026-01-09 18:40:04', '2026-01-09 21:20:40', 497, 0, NULL),
(50, 12, 'Foundation', 'Isaac Asimov', '9780008117498', 2016, 'Fiction', 'WINNER OF THE HUGO AWARD FOR BEST ALL-TIME SERIES The Foundation series is Isaac Asimov\'s iconic masterpiece. Unfolding against the backdrop of a crumbling Galactic Empire, the story of Hari Seldon\'s two Foundations is a lasting testament to an extraordinary imagination, one that shaped science fiction as we know it today. The Galactic Empire has prospered for twelve thousand years. Nobody suspects that the heart of the thriving Empire is rotten, until psychohistorian Hari Seldon uses his new science to foresee its terrible fate. Exiled to the desolate planet Terminus, Seldon establishes a colony of the greatest minds in the Empire, a Foundation which holds the key to changing the fate of the galaxy. However, the death throes of the Empire breed hostile new enemies, and the young Foundation\'s fate will be threatened first.', 'http://books.google.com/books/content?id=PKV6swEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=PKV6swEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Unknown', '2026-01-09 18:41:40', '2026-01-09 21:22:59', 0, 0, NULL),
(51, 12, 'Alice in Borderland, Vol. 1', 'Haro Aso', '9781974728374', 2022, 'Comics & Graphic Novels', 'An action-packed thriller and source of the hit Netflix drama where the only way to survive is to play the game! Battle Royale: Angel\'s Border; Deadman Wonderland; Death Note; Zom 100 The first game starts with a bang, but Ryohei manages to beat the clock and save his friends. It’s a short-lived victory, however, as they discover that winning only earns them a few days’ grace period. If they want to get home, they’re going to have to start playing a lot harder.', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Good', '2026-01-09 18:50:16', '2026-01-09 22:02:39', 344, 0, NULL),
(52, 12, 'Alice in Borderland, Vol. 1', 'Haro Aso', '9781974728374', 2022, 'Comics & Graphic Novels', 'An action-packed thriller and source of the hit Netflix drama where the only way to survive is to play the game! Battle Royale: Angel\'s Border; Deadman Wonderland; Death Note; Zom 100 The first game starts with a bang, but Ryohei manages to beat the clock and save his friends. It’s a short-lived victory, however, as they discover that winning only earns them a few days’ grace period. If they want to get home, they’re going to have to start playing a lot harder.', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Good', '2026-01-09 18:50:40', '2026-01-09 22:30:09', 344, 0, NULL),
(53, 12, 'Alice in Borderland, Vol. 1', 'Haro Aso', '9781974728374', 2022, 'Comics & Graphic Novels', 'An action-packed thriller and source of the hit Netflix drama where the only way to survive is to play the game! Battle Royale: Angel\'s Border; Deadman Wonderland; Death Note; Zom 100 The first game starts with a bang, but Ryohei manages to beat the clock and save his friends. It’s a short-lived victory, however, as they discover that winning only earns them a few days’ grace period. If they want to get home, they’re going to have to start playing a lot harder.', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Fair', '2026-01-09 18:50:58', '2026-01-09 19:07:19', 344, 1, 'sSasAS'),
(61, 14, 'Jurassic Park', 'Michael Crichton', '0099282917', 1991, 'Fiction', 'On a remote jungle island, genetic engineers have created a dinosaur game park. An astonishing technique for recovering and cloning dinosaur DNA has been discovered. Now one of mankind\'s most thrilling fantasies has come true and the first dinosaurs that the Earth has seen in the time of man emerge. But, as always, there is a dark side to the fantasy and after a catastrophe destroys the park\'s defence systems, the scientists and tourists are left fighting for survival.', 'http://books.google.com/books/content?id=iqfNIpyLHrgC&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=iqfNIpyLHrgC&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Good', '2026-01-12 19:36:30', '2026-03-30 22:17:29', 416, 0, ''),
(62, 14, 'Project Hail Mary', 'Andy Weir', '0593135229', 2022, 'Fiction', 'THE #1 NEW YORK TIMES BESTSELLER FROM THE AUTHOR OF THE MARTIAN • Soon to be a major motion picture starring Ryan Gosling, directed by Phil Lord and Christopher Miller, with a screenplay by Drew Goddard A lone astronaut must save the earth from disaster in this “propulsive” (Entertainment Weekly), cinematic thriller full of suspense, humor, and fascinating science. HUGO AWARD FINALIST • ONE OF THE YEAR’S BEST BOOKS: Bill Gates, GatesNotes, New York Public Library, Parade, Newsweek, Polygon, Shelf Awareness, She Reads, Kirkus Reviews, Library Journal • New York Times Readers Pick: 100 Best Books of the 21st Century “An epic story of redemption, discovery and cool speculative sci-fi.”—USA Today “If you loved The Martian, you’ll go crazy for Weir’s latest.”—The Washington Post Ryland Grace is the sole survivor on a desperate, last-chance mission—and if he fails, humanity and the earth itself will perish. Except that right now, he doesn’t know that. He can’t even remember his own name, let alone the nature of his assignment or how to complete it. All he knows is that he’s been asleep for a very, very long time. And he’s just been awakened to find himself millions of miles from home, with nothing but two corpses for company. His crewmates dead, his memories fuzzily returning, Ryland realizes that an impossible task now confronts him. Hurtling through space on this tiny ship, it’s up to him to puzzle out an impossible scientific mystery—and conquer an extinction-level threat to our species. And with the clock ticking down and the nearest human being light-years away, he’s got to do it all alone. Or does he? An irresistible interstellar adventure as only Andy Weir could deliver, Project Hail Mary is a tale of discovery, speculation, and survival to rival The Martian—while taking us to places it never dreamed of going.', 'http://books.google.com/books/content?id=iEiHEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=iEiHEAAAQBAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Good', '2026-01-12 19:42:25', '2026-01-12 19:42:25', 497, 1, 'a'),
(63, 14, 'Alice in Borderland, Vol. 1', 'Haro Aso', '9781974728374', 2022, 'Comics & Graphic Novels', 'An action-packed thriller and source of the hit Netflix drama where the only way to survive is to play the game! Battle Royale: Angel\'s Border; Deadman Wonderland; Death Note; Zom 100 The first game starts with a bang, but Ryohei manages to beat the clock and save his friends. It’s a short-lived victory, however, as they discover that winning only earns them a few days’ grace period. If they want to get home, they’re going to have to start playing a lot harder.', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'New', '2026-01-12 19:48:42', '2026-02-24 00:12:05', 344, 0, 'kk'),
(64, 12, 'Management Information Systems', 'Kenneth C. Laudon, Jane Price Laudon', '9781292403281', 2021, 'Business & Economics', 'Management Information Systems, 17th edition has been thoroughly updated to cover the latest industry and technology changes that impact the course', 'http://books.google.com/books/content?id=AqJXzgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=AqJXzgEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'New', '2026-01-12 20:18:00', '2026-01-12 20:18:00', 0, 1, ''),
(65, 12, 'Pride and Prejudice', 'Jane Austen', '0141439513', 2002, 'Fiction', 'Austen\'s most popular novel, the unforgettable story of Elizabeth Bennet and Mr. Darcy Few have failed to be charmed by the witty and independent spirit of Elizabeth Bennet in Austen’s beloved classic Pride and Prejudice. When Elizabeth Bennet first meets eligible bachelor Fitzwilliam Darcy, she thinks him arrogant and conceited; he is indifferent to her good looks and lively mind. When she later discovers that Darcy has involved himself in the troubled relationship between his friend Bingley and her beloved sister Jane, she is determined to dislike him more than ever. In the sparkling comedy of manners that follows, Jane Austen shows us the folly of judging by first impressions and superbly evokes the friendships, gossip and snobberies of provincial middle-class life. This Penguin Classics edition, based on Austen\'s first edition, contains the original Penguin Classics introduction by Tony Tanner and an updated introduction and notes by Viven Jones. For more than seventy years, Penguin has been the leading publisher of classic literature in the English-speaking world. With more than 1,700 titles, Penguin Classics represents a global bookshelf of the best works throughout history and across genres and disciplines. Readers trust the series to provide authoritative texts enhanced by introductions and notes by distinguished scholars and contemporary authors, as well as up-to-date translations by award-winning translators.', 'http://books.google.com/books/content?id=uY6MEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=uY6MEAAAQBAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'New', '2026-01-13 17:29:21', '2026-01-13 17:29:40', 0, 0, ''),
(66, 1, 'Designing Interfaces', 'Jenifer Tidwell, Charles Brewer, Aynne Valencia-Brooks', '9781492051961', 2020, 'Computers', 'Designing good application interfaces isn\'t easy now that companies need to create compelling, seamless user experiences across an exploding number of channels, screens, and contexts. In this updated third edition, you\'ll learn how to navigate through the maze of design options. By capturing UI best practices as design patterns, this best-selling book provides solutions to common design problems. You\'ll learn patterns for mobile apps, web applications, and desktop software. Each pattern contains full-color examples and practical design advice you can apply immediately. Experienced designers can use this guide as an idea sourcebook, and novices will find a road map to the world of interface and interaction design. Understand your users before you start designing Build your software\'s structure so it makes sense to users Design components to help users complete tasks on any device Learn how to promote wayfinding in your software Place elements to guide users to information and functions Learn how visual design can make or break product usability Display complex data with artful visualizations', 'http://books.google.com/books/content?id=agTBxQEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=agTBxQEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'New', '2026-01-13 20:48:58', '2026-01-13 20:48:58', 0, 1, 'Ewwww!!!'),
(67, 15, '50 Math Tricks That Will Change Your Life', 'Tanya Zakowich', '1645678288', 2023, 'Education', 'Change the way you look at math forever, with these insane tricks that make working with numbers fun and exciting for all ages! This incredible collection will teach you how to do equations in a flash (no calculator required!) to help with everyday questions and situations—from figuring out how many hours you spent at school last year, to who pays what at a restaurant. Impress your friends with your quick problem solving, and foster an appreciation for numbers as you master each trick. Tanya Zakowich, creator of Pink Pencil math, is an online instructor with a knack for making math easy and enjoyable for everyone, students and parents alike. She carefully walks you through every equation and question, and offers tons of helpful examples so you never feel lost. Practice right in the pages of the book, and take it on the go for a fun and educational pass time. You’ll be amazed to discover all the easy ways you can use concepts like percentages, long division, times tables, square roots and more to help with day-to-day life. And with Tanya’s infectious love of math, it’s hard not to fall in love with the subject as you learn from her—whether you’re currently studying it in school or did so decades ago!', 'http://books.google.com/books/content?id=vU6mEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=vU6mEAAAQBAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Good', '2026-01-13 23:15:16', '2026-01-13 23:15:16', 0, 1, 'Great Meth Tricks!'),
(68, 15, 'Time Shelter', 'Georgi Gospodinov', '1324090952', 2022, 'Fiction', 'An award-winning international sensation—with a second-act dystopian twist—Time Shelter is a tour de force set in a world clamoring for the past before it forgets. “At one point they tried to calculate when time began, when exactly the earth had been created,” begins Time Shelter’s enigmatic narrator, who will go unnamed. “In the mid–seventeenth century, the Irish bishop Ussher calculated not only the exact year, but also a starting date: October 22, 4,004 years before Christ.” But for our narrator, time as he knows it begins when he meets Gaustine, a “vagrant in time” who has distanced his life from contemporary reality by reading old news, wearing tattered old clothes, and haunting the lost avenues of the twentieth century. In an apricot-colored building in Zurich, surrounded by curiously planted forget-me-nots, Gaustine has opened the first “clinic for the past,” an institution that offers an inspired treatment for Alzheimer’s sufferers: each floor reproduces a past decade in minute detail, allowing patients to transport themselves back in time to unlock what is left of their fading memories. Serving as Gaustine’s assistant, the narrator is tasked with collecting the flotsam and jetsam of the past, from 1960s furniture and 1940s shirt buttons to nostalgic scents and even wisps of afternoon light. But as the charade becomes more convincing, an increasing number of healthy people seek out the clinic to escape from the dead-end of their daily lives—a development that results in an unexpected conundrum when the past begins to invade the present. Through sharply satirical, labyrinth-like vignettes reminiscent of Italo Calvino and Franz Kafka, the narrator recounts in breathtaking prose just how he became entrenched in a plot to stop time itself. “A trickster at heart, and often very funny” (Garth Greenwell, The New Yorker), prolific Bulgarian author Georgi Gospodinov masterfully stalks the tragedies of the last century, including our own, in what becomes a haunting and eerily prescient novel teeming with ideas. Exquisitely translated by Angela Rodel, Time Shelter is a truly unforgettable classic from “one of Europe’s most fascinating and irreplaceable novelists” (Dave Eggers).', 'http://books.google.com/books/content?id=l2yPEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=l2yPEAAAQBAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Fair', '2026-01-13 23:17:49', '2026-01-13 23:17:49', 0, 1, ''),
(69, 12, 'Pride and Prejudice', 'Jane Austen', '0141439513', 2002, 'Fiction', 'Austen\'s most popular novel, the unforgettable story of Elizabeth Bennet and Mr. Darcy Few have failed to be charmed by the witty and independent spirit of Elizabeth Bennet in Austen’s beloved classic Pride and Prejudice. When Elizabeth Bennet first meets eligible bachelor Fitzwilliam Darcy, she thinks him arrogant and conceited; he is indifferent to her good looks and lively mind. When she later discovers that Darcy has involved himself in the troubled relationship between his friend Bingley and her beloved sister Jane, she is determined to dislike him more than ever. In the sparkling comedy of manners that follows, Jane Austen shows us the folly of judging by first impressions and superbly evokes the friendships, gossip and snobberies of provincial middle-class life. This Penguin Classics edition, based on Austen\'s first edition, contains the original Penguin Classics introduction by Tony Tanner and an updated introduction and notes by Viven Jones. For more than seventy years, Penguin has been the leading publisher of classic literature in the English-speaking world. With more than 1,700 titles, Penguin Classics represents a global bookshelf of the best works throughout history and across genres and disciplines. Readers trust the series to provide authoritative texts enhanced by introductions and notes by distinguished scholars and contemporary authors, as well as up-to-date translations by award-winning translators.', 'http://books.google.com/books/content?id=uY6MEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=uY6MEAAAQBAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Good', '2026-01-16 00:09:24', '2026-01-16 00:09:24', 0, 1, ''),
(79, 22, 'Atomic Habits', 'James Clear', '9781847941831', 2018, '', '________________________________ \'A supremely practical and useful book. James Clear distils the most fundamental information about habit formation, so you can accomplish more by focusing on less.\' Mark Manson, author of The Subtle Art of Not Giving A F*ck ________________________________ A revolutionary system to get 1 per cent better every day People think when you want to change your life, you need to think big. But world-renowned habits expert James Clear has discovered another way. He knows that real change comes from the compound effect of hundreds of small decisions - doing two push-ups a day, waking up five minutes early, or holding a single short phone call. He calls them atomic habits. In this ground-breaking book, Clears reveals exactly how these minuscule changes can grow into such life-altering outcomes. He uncovers a handful of simple life hacks (the forgotten art of Habit Stacking, the unexpected power of the Two Minute Rule, or the trick to entering the Goldilocks Zone), and delves into cutting-edge psychology and neuroscience to explain why they matter. Along the way, he tells inspiring stories of Olympic gold medalists, leading CEOs, and distinguished scientists who have used the science of tiny habits to stay productive, motivated, and happy. These small changes will have a revolutionary effect on your career, your relationships, and your life. ________________________________ \'James Clear has spent years honing the art and studying the science of habits. This engaging, hands-on book is the guide you need to break bad routines and make good ones.\' Adam Grant, author of Originals \'A special book that will change how you approach your day and live your life.\' Ryan Holiday, author of The Obstacle is the Way', 'http://books.google.com/books/content?id=GuAUMQAACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=GuAUMQAACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Good', '2026-01-16 22:16:23', '2026-02-21 20:53:25', 320, 0, 'Did not pick up any good habits. Skill issue, I guess :(.'),
(81, 21, 'The Subtle Art of Not Giving a F**k', 'Mark Manson', '9780062457714', 2016, 'Self-Help', '', 'http://books.google.com/books/content?id=RobZjgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=RobZjgEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'New', '2026-01-18 18:40:25', '2026-01-18 18:40:25', 304, 1, ''),
(82, 21, 'Music Theory: A Practical Approach To Quickly Understand Music Theory in a Step-By-Step Way. Learn How to Read And Compose Music For', 'Ludwig Copson', '9781914028229', 2020, 'Music', 'Have you ever had trouble learning music theory or thought that it\'s too hard to learn? Do you want to learn music theory and master scales and chords? Can you imagine your friend\'s surprise when you will get to entertain them playing their favorite songs? If yes, then keep reading. Music is the most ancient form of art that fascinates humanity from the beginning of its origins and awakens strong emotions in all of us. Music is the language of the soul, we associate all the moments of our lives with it, how many times have you been listening to music remembering certain people or places? Music is based on some universal rules, the rules of harmony, intervals and proportions, that are all contained and explained clearly in this extraordinary book \"Music Theory\", that provides the perfect start for someone who has learned their basic first notes and maybe learned a few songs here and there but doesn\'t know why a chord is spelled the way it is (e.g., why a C Major has a C, E, and a G?). Or they may have problem learning rhythms. This book is meant to take you forward on your musical journey. In this first part of the series, we will cover the absolute fundamentals of music theory and music reading. We will also cover first position basic chords and the first scale in C Major. With each step, you will also get to learn a practical example where you will be able to apply what you just learned in that particular step. Let us look at some very important things you will learn in this book Fundamentals of Music Theory A simple method to read music notes The easiest ways to grasp the concepts of rhythm, harmony and intervals How to find out what chords to use The perfect ways to write better melodies What are the most common scales used in music What are common musical terms Quick ways to understand augmentation and diminution Powerful and useful tips and tricks for ear training And much more Many people think that Music Theory is something complicated and sometimes boring, that might limit the creativity. Nothing could be farther away from the truth. Music theory defines the fundamental aspects of music and provides musicians a system to communicate their ideas. This book has been conceived for a practical approach, at every step you will also learn a sample song. All the notions are clearly explained and all the topics are structured in the best way to bring your knowledge to an optimal level in a small amount of time. Would You Like To Know More?Buy Now to get started!', 'http://books.google.com/books/content?id=kt37zQEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=kt37zQEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Good', '2026-01-18 18:43:05', '2026-01-18 18:43:05', 274, 1, ''),
(83, 22, 'Brave New World', 'Aldous Huxley', '0099518473', 2007, 'Fiction', 'Welcome to New London. Everybody is happy here. INTRODUCED BY MARGARET ATWOOD Our perfect society achieves peace and stability by dispensing with monogamy, privacy, money, family and history itself. Now everyone belongs. You can be happy too. All you need to do is take your Soma pills. This is the brave new world of Aldous Huxley\'s deeply sinister and prophetic novel, a society based on maximum pleasure and complete surveillance - no matter the cost. \'A masterpiece of speculation... As vibrant, fresh, and somehow shocking as it was when I first read it\' Margaret Atwood, bestselling author of The Handmaid\'s Tale \'A grave warning... Provoking, stimulating, shocking and dazzling\' Observer \'Huxley\'s great dystopian novel\' Guardian **One of the BBC\'s 100 Novels That Shaped Our World**', 'http://books.google.com/books/content?id=3zl4oJMUskoC&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=3zl4oJMUskoC&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'New', '2026-01-18 18:44:13', '2026-01-18 18:44:13', 290, 1, ''),
(84, 22, '1984 Nineteen Eighty-Four', 'George Orwell', '9780008322069', 2021, 'Fiction', 'One of the BBC\'s \'100 Novels That Shaped Our World\' HarperCollins is proud to present its incredible range of best-loved, essential classics. Winston Smith rewrites history. It\'s his job. Hidden away in the Record Department of the sprawling Ministry of Truth, he helps the Party, and the omnipresent Big Brother, control the people of Oceania. Winston knows what a good citizen of Oceania must do: show his devotion for Big Brother and the Party; abstain from all vices; and, most importantly, possess no critical thoughts of their own. The new notebook he\'s begun to write in is definitely against the rules - in fact, the Thought Police could arrest him simply for having it. Yet, as Winston begins to write his own history, a seed of rebellion begins to grow in his heart - one that could have devastating consequences. In George Orwell\'s final and most well-known novel, he explores a dystopian future in which a totalitarian government controls the actions, thoughts and even emotions of its citizens, exercising power through control of language and history. Its lasting popularity is testament to Orwell\'s powerful prose, and is a passionate political warning for today.', 'http://books.google.com/books/content?id=-VmgzQEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=-VmgzQEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Good', '2026-01-18 18:46:00', '2026-01-18 18:46:00', 0, 1, ''),
(85, 22, 'Project Hail Mary', 'Andy Weir', '9781529157468', 2022, '', 'AS SEEN ON BARACK OBAMA\'S 2021 SUMMER READING LIST \'THE MOST ENJOYABLE HARD SF I HAVE READ IN YEARS\' THE GUARDIAN OUT NOW from the bestselling author of THE MARTIAN \'Weir\'s finest work to date. . . This is the one book I read last year that I am certain I can recommend to anyone, no matter who, and know they\'ll love it.\' BRANDON SANDERSON \'If you like a lot of science in your science fiction, Andy Weir is the writer for you. . . This one has everything fans of old school SF (like me) love.\' GEORGE R.R. MARTIN \'Brilliantly funny and enjoyable. One of the most plausible science fiction books I\'ve ever read\' TIM PEAKE, astronaut ________________________________________ A lone astronaut. An impossible mission. An ally he never imagined. Ryland Grace is the sole survivor on a desperate, last-chance mission - and if he fails, humanity and the earth itself will perish. Except that right now, he doesn\'t know that. He can\'t even remember his own name, let alone the nature of his assignment or how to complete it. All he knows is that he\'s been asleep for a very, very long time. And he\'s just been awakened to find himself millions of miles from home, with nothing but two corpses for company. His crewmates dead, his memories fuzzily returning, Ryland realizes that an impossible task now confronts him. Hurtling through space on this tiny ship, it\'s up to him to puzzle out an impossible scientific mystery-and conquer an extinction-level threat to our species. And with the clock ticking down and the nearest human being light-years away, he\'s got to do it all alone. Or does he? An irresistible interstellar adventure as only Andy Weir could imagine it, Project Hail Mary is a tale of discovery, speculation, and survival to rival The Martian -- while taking us to places it never dreamed of going. ________________________________________ \'One of the most original, compelling, and fun voyages I\'ve ever taken.\' ERNEST CLINE, author of Ready Player One and Ready Player Two \'Undisputedly the best book I\'ve read in a very, very long time. Mark my words: Project Hail Mary is destined to become a classic.\' BLAKE CROUCH \'Andy Weir\'s brilliant Project Hail Mary...is one of those stirring sci-fi novels about every government on Earth banding together, through science, to save civilisation from collapse. I loved it.\' THE TIMES \'A suspenseful portrait of human ingenuity and resilience [that] builds to an unexpectedly moving ending. A winner.\' PUBLISHERS WEEKLY \'Weir returns with gusto . . . his writing flows naturally, and his characters and dialogue crackle with energy. With this novel, he takes his place as a genuine star in the mainstream SF world.\' BOOKLIST', 'http://books.google.com/books/content?id=eFSjzgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=eFSjzgEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'New', '2026-01-18 18:54:03', '2026-01-18 18:54:03', 448, 1, 'Awesome 😎'),
(86, 12, 'Alice in Borderland, Vol. 1', 'Haro Aso', '9781974728374', 2022, 'Comics & Graphic Novels', 'An action-packed thriller and source of the hit Netflix drama where the only way to survive is to play the game! Battle Royale: Angel\'s Border; Deadman Wonderland; Death Note; Zom 100 The first game starts with a bang, but Ryohei manages to beat the clock and save his friends. It’s a short-lived victory, however, as they discover that winning only earns them a few days’ grace period. If they want to get home, they’re going to have to start playing a lot harder.', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=IgyMzgEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'New', '2026-03-20 18:34:35', '2026-03-30 16:41:50', 344, 0, ''),
(87, 12, 'Jurassic Park', 'Michael Crichton', '0099282917', 1991, 'Fiction', 'On a remote jungle island, genetic engineers have created a dinosaur game park. An astonishing technique for recovering and cloning dinosaur DNA has been discovered. Now one of mankind\'s most thrilling fantasies has come true and the first dinosaurs that the Earth has seen in the time of man emerge. But, as always, there is a dark side to the fantasy and after a catastrophe destroys the park\'s defence systems, the scientists and tourists are left fighting for survival.', 'http://books.google.com/books/content?id=iqfNIpyLHrgC&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=iqfNIpyLHrgC&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'New', '2026-03-20 18:38:34', '2026-03-30 16:41:13', 416, 0, ''),
(88, 12, 'Pride and Prejudice', 'Jane Austen', '0141439513', 2002, 'Fiction', 'Austen\'s most popular novel, the unforgettable story of Elizabeth Bennet and Mr. Darcy Few have failed to be charmed by the witty and independent spirit of Elizabeth Bennet in Austen’s beloved classic Pride and Prejudice. When Elizabeth Bennet first meets eligible bachelor Fitzwilliam Darcy, she thinks him arrogant and conceited; he is indifferent to her good looks and lively mind. When she later discovers that Darcy has involved himself in the troubled relationship between his friend Bingley and her beloved sister Jane, she is determined to dislike him more than ever. In the sparkling comedy of manners that follows, Jane Austen shows us the folly of judging by first impressions and superbly evokes the friendships, gossip and snobberies of provincial middle-class life. This Penguin Classics edition, based on Austen\'s first edition, contains the original Penguin Classics introduction by Tony Tanner and an updated introduction and notes by Viven Jones. For more than seventy years, Penguin has been the leading publisher of classic literature in the English-speaking world. With more than 1,700 titles, Penguin Classics represents a global bookshelf of the best works throughout history and across genres and disciplines. Readers trust the series to provide authoritative texts enhanced by introductions and notes by distinguished scholars and contemporary authors, as well as up-to-date translations by award-winning translators.', 'http://books.google.com/books/content?id=uY6MEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=uY6MEAAAQBAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'New', '2026-03-20 18:51:12', '2026-03-20 20:12:21', 0, 0, ''),
(89, 12, 'Legendborn', 'Tracy Deonn', '1534441603', 2020, 'Young Adult Fiction', 'An Instant New York Times Bestseller! Winner of the Coretta Scott King - John Steptoe for New Talent Author Award Filled with mystery and an intriguingly rich magic system, Tracy Deonn’s YA contemporary fantasy Legendborn offers the dark allure of City of Bones with a modern-day twist on a classic legend and a lot of Southern Black Girl Magic. After her mother dies in an accident, sixteen-year-old Bree Matthews wants nothing to do with her family memories or childhood home. A residential program for bright high schoolers at UNC–Chapel Hill seems like the perfect escape—until Bree witnesses a magical attack her very first night on campus. A flying demon feeding on human energies. A secret society of so called “Legendborn” students that hunt the creatures down. And a mysterious teenage mage who calls himself a “Merlin” and who attempts—and fails—to wipe Bree’s memory of everything she saw. The mage’s failure unlocks Bree’s own unique magic and a buried memory with a hidden connection: the night her mother died, another Merlin was at the hospital. Now that Bree knows there’s more to her mother’s death than what’s on the police report, she’ll do whatever it takes to find out the truth, even if that means infiltrating the Legendborn as one of their initiates. She recruits Nick, a self-exiled Legendborn with his own grudge against the group, and their reluctant partnership pulls them deeper into the society’s secrets—and closer to each other. But when the Legendborn reveal themselves as the descendants of King Arthur’s knights and explain that a magical war is coming, Bree has to decide how far she’ll go for the truth and whether she should use her magic to take the society down—or join the fight.', 'http://books.google.com/books/content?id=gU_4DwAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 'http://books.google.com/books/content?id=gU_4DwAAQBAJ&printsec=frontcover&img=1&zoom=5&edge=curl&source=gbs_api', 'Fair', '2026-03-27 14:25:44', '2026-03-30 16:50:05', 512, 1, 'asraws'),
(90, 1, 'Cracking the Coding Interview, 6th Edition', 'Gayle Laakmann McDowell', '9780984782864', 2015, '', '', '', '', 'New', '2026-03-30 22:33:31', '2026-03-30 22:34:27', 0, 0, ''),
(91, 1, 'Beyond Cracking the Coding Interview', 'Gayle Laakmann McDowell, Mike Mroczka, Aline Lerner, Nil Mamano', '9781955706001', 2025, 'Business & Economics', 'For over a decade, Cracking the Coding Interview has been hailed as the \"bible\" of interview prep. Now, Beyond Cracking the Coding Interview builds on that foundation to prepare you for today\'s tougher technical interviews and hiring climate. * 13 New Chapters and Expansions: Including topics such as two pointers, sliding windows, monotonic stacks & queues, prefix sums, heaps, and greedy algorithms. * 150+ New Problems: Ranging from fresh takes on old classics to brand-new algorithmic problems. * Triggers and Boosters: How to solve any question with boundary thinking (Big O and beyond), trigger analysis, and our top five problem-solving boosters. * Interview Replays: Watch close to a hundred interview replays, drawn from interviewing.io\'s collection of FAANG mock interviews. * Data-Driven Approaches to the Soft Squishy Stuff: Go deep into how to land interviews at top-tier companies, properly time your job search, master behavioral questions, and negotiate a better offer. And learn exactly what to say in most hiring situations you\'re likely to encounter.', 'http://books.google.com/books/content?id=BrcZ0QEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'http://books.google.com/books/content?id=BrcZ0QEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api', 'Good', '2026-03-30 22:38:36', '2026-03-30 22:38:36', 0, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `book_swap_requests`
--

CREATE TABLE `book_swap_requests` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `requester_id` int(11) DEFAULT NULL,
  `owner_action_token` longtext DEFAULT NULL,
  `requester_action_token` longtext DEFAULT NULL,
  `status` enum('PENDING','SHIPPINGPAID','SHIPPED','DELIVERED','COMPLETED','TAKENDOWN') NOT NULL DEFAULT 'PENDING',
  `shipping_street` varchar(100) DEFAULT NULL,
  `shipping_post_code` varchar(15) DEFAULT NULL,
  `shipping_state` varchar(50) DEFAULT NULL,
  `shipping_country` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `shipping_cost` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `book_swap_requests`
--

INSERT INTO `book_swap_requests` (`id`, `book_id`, `owner_id`, `requester_id`, `owner_action_token`, `requester_action_token`, `status`, `shipping_street`, `shipping_post_code`, `shipping_state`, `shipping_country`, `created_at`, `closed_at`, `shipping_cost`) VALUES
(71, 40, 1, 12, 'ZTM1ZjI3YmI1NTJmNjE0ODg3NTAyYjdkMmNhYzU2N2UwZWMyNjBiOGU1MzdkNzYzMmVmZmZiMDlhZTU5NDFhMw==', 'Yjg2N2VkZjMyMTY3MjFhYWFhZDYxNTM0M2I2YTBiOGVlYjkxNzhkMmM3ODcwNDlhM2RkYmI4MDlkM2I1MTllOQ==', 'COMPLETED', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2025-12-29 22:54:44', '2026-01-13 18:40:44', 20),
(72, 41, 1, 12, 'NTllOThjMDM4NGE0NjUzZDQ2YThkNmViMzhmZjBkNWFjYzU3MTRmYWI3ZWNjMmNjMTgyOGJmMjU5ODk1OWE4Mw==', 'NmY5YzIxNjk0YzJmOGU4MzIwNGZlMWIxMzVlZTM1ZTQ0NTljZGEyZWNlYWUxMjQzMjljYWExMzdkYTliOWMwNQ==', 'COMPLETED', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2025-12-29 22:55:41', '2026-01-13 18:40:32', 8),
(74, 42, 12, 1, 'OTcxZjI3OTE0OGQ5YTZjNmE3NTQzMDE2Y2YzYTUxYzA5ODAwZjdlOTVlNjNlZTk5MWRmMmJjY2ZhNDFkMTQ4Nw==', 'NDY2YTNhYjU2OWEzYTQwMDUwNWNiODY4MDNhODIyNjg0OWU0ZDJkZDc1ODNmYmI2ODBmNWFmODk1NzA1MGM4ZQ==', 'DELIVERED', 'lel12', '1415zd', 'Amsterdam', 'Nl', '2025-12-30 00:43:08', NULL, 8),
(76, 40, 1, 12, 'NTllOThjMDM4NGE0NjUzZDQ2YThkNmViMzhmZjBkNWFjYzU3MTRmYWI3ZWNjMmNjMTgyOGJmjsU5ODk1OWE4Mw==', 'NmY5YzIxNjk0YzJmOGU4MzIwNGZlMWIxMzVlZTM1ZTQ0NTljZGEyZWNlYWUxMjQzMjljYWExMzdkYTliOWMwdNQ==', 'COMPLETED', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2025-12-29 22:55:41', '2026-01-13 18:40:40', 8),
(78, 44, 12, 1, 'MmM2MmY2OTIwN2YxMjhiN2MzMjMyOTc3NjBkNzRlY2NkZDY1NTEzOGQ3NjY0ZjQyZWJjNmQ0NThiZWY0NTU0Yw==', 'YzBlNjEzZGNkMzljZGYwNGNhNTBiMDBkODM5NDc2ZjFiOTE1YWY5YzFhNTU3Nzk5YjVjZjFmMjNjNTVkMTAwYQ==', 'SHIPPED', 'NaMaikaTi', '1415xd', 'Amsterdam', 'Netherlands', '2025-12-30 22:42:46', NULL, 8),
(80, 46, 12, 1, 'MTY1MjExZGI3Y2RiOWI2NGJiM2IwMmFjOWU5ZjI4YzIwNmQxZDAwMmU2NTFlOTc0ZGRhYmIyNzU3MGEzZThlNA==', 'MzA1MTMyMTk3OTRkNzY0ZTc2YWEzNWUxYmRhYjljNzkyNjM5NWY1ZTc1OWIzNGY4M2NlMjI2Y2Y1ZjA2Yzc2Yg==', 'SHIPPED', 'lel12', '1415zd', 'Amsterdam', 'Nl', '2025-12-30 23:24:09', NULL, 8),
(81, 47, 1, 12, 'NDhiOTQ5YTZkM2Y2OWEwNTk1ZjIwZjYxZDI3YTQzYTFjMzY2MThmOGM2YTJiZGE5OTY4YmUyOTg1MjYzZDEyNA==', 'MGMzNzY1ZjAwZTIyMzliMzdhZjI5OGYxYjI3ZTEzNjBlMTQ3NDdkYzJiYmY0YjUwMDhjODkzYTg0MTBhNjdhNw==', 'COMPLETED', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2026-01-09 10:52:11', '2026-01-13 18:40:16', 8),
(82, 48, 12, NULL, NULL, NULL, 'TAKENDOWN', NULL, NULL, NULL, NULL, '2026-01-09 18:26:47', NULL, NULL),
(83, 49, 12, 1, 'MjJiY2YwZGNkM2Y3MTNkMzFjZDE4NjAwMmNjZWExODUyMGNkMmM3ZmIwMjgwYWIyOGI1NGVkNjgyOTMxYTNlMA==', 'NTRmMzk1NGU3MzkwM2ZmYmY0ZGJhNzRlZGU5NDM3MjNjY2YwOWQ2YmI5YTdhNDdkM2FkMTgyZmE4NDM4NTQwYg==', 'SHIPPED', 'lel12', '1415zd', 'Amsterdam', 'Nl', '2026-01-09 18:40:04', NULL, 8),
(84, 50, 12, 1, 'NGFhMWM5ZmFkOTk0NmQzYWE2YzJhZDZmNzZhMmRhZTAyMjhlNjUzZDE2MDQyODJmMjljZWY1NmRlOTg1NzJhZA==', 'OTgxY2Y0ODAwODA2ZGY0MmYyYjgxODE2ZDUxMjk4N2E5NjFhMTliMDQ0YTA4YmI5M2Q3YTIzNzA3OWI1ODNjNQ==', 'COMPLETED', 'lel12', '1415zd', 'Amsterdam', 'Nl', '2026-01-09 18:41:40', '2026-02-24 00:15:49', 8),
(85, 51, 12, 1, 'YmI5MjAwMDNhN2E5MmUxNjQ2YWNjYjg5YmQwNjkyMmU5NWZiNjZjMGQyNjY1NzhhY2EwMjQzODQ1NTVhMGQxMw==', 'ZDVhN2FhNDJkMjU2MDM4NDliZTc3YjFhMTkwYzkxMmJjMTU1ZDE1MWQ2ZTgxZTYzZjlkMGMzNGQ5MTNiN2M0ZQ==', 'COMPLETED', 'lel12', '1415zd', 'Amsterdam', 'Nl', '2026-01-09 18:50:17', '2026-02-24 00:15:23', 8),
(86, 52, 12, 1, 'ZDJlMDMyYjc3NzZjNThlODdlZGI2NjdmNjQ4ZTBmYzRkYzE3MTBlOTNjNGI0MTJhM2Y3MDkyYmIyZjQwNDVlOQ==', 'ZmE0MTNjMjY4ZTUxOTYzN2FkYzMyZDUyYTc3NzJhY2IyMWNlYWU2YjBhNmU4MTBlMzk3MWE5YWVhYjhiNjcyMw==', 'COMPLETED', 'lel12', '1415zd', 'Amsterdam', 'Nl', '2026-01-09 18:50:40', '2026-02-21 20:53:45', 5),
(95, 61, 14, 12, 'MjAwM2NjNGNhYzMwZmZjMTIzOThlMGYyM2RiZGFhZWU5ODFkZTBiMzJiZWY0MTQ1YzYxZTUxMWUxZmMzYjM0ZA==', 'YWM5NTE2ZDBjNmMxOTk1NjRjYThiMjhiMDYxZTZmYTM5MGRhM2ZiMmI2NDY0NWZlMTgzZTc1MTJmNTJkMzgyYQ==', 'SHIPPINGPAID', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2026-01-12 19:36:30', NULL, 20),
(96, 62, 14, 12, 'MTU0Yzc5NGExMzU1NjU0ODFjYTE4NDNhYTgzZWFiMjUwZTMzZWY5NGYzM2ZhNDIzYmViMTFhMmFiOTMwYmFkZA==', 'YjgxZDhjMjY3YTc3ZDdiMDc2NjBjNjg5NmI5NDhjMGRhYjBiMjgyMzk3MjRkYzA5ZWM5ZGI3OGVhNzRiYTgyOQ==', 'PENDING', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2026-01-12 19:42:25', NULL, 20),
(97, 63, 14, 12, 'MjM3MzllZGIyYjMzYTUwOTAyODM1ODllZTBkYzJjMjMxMmU4NTAwYmRlZWFiN2Y5MTRiMTg0MDI0MzFjYWIxNw==', 'OTU2ZmNlOTQ3ZjQ4NDNhZTAyMWI4Mzg5NzM5OWQ4Njg3OTcyY2NjOWQ2ZThhZjMwZGMxZGY0YzY2YWUyN2M3Ng==', 'SHIPPED', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2026-01-12 19:48:43', NULL, 8),
(98, 64, 12, 14, 'ZjgwMjA4M2YzYTgyMzk5YzMyYjdlMDNjMGU1ZTc1MjU1YzMwNzBlYzczY2E4YmY3ZjYxOTM4ZGIyNWI0Mzg4MA==', 'YjkwMWRkNTIwNzUyNjg1NDUzNjkxYWE0MGMxMjY4MzdjYjY3ZmU2ZjUwYTY1YmFjMTExNmYyNGViMTBiYTU0Ng==', 'PENDING', 'Deje', '1057cm', 'Koog aan de Zaan', 'Netherlands ', '2026-01-12 20:18:00', NULL, 8),
(99, 65, 12, NULL, NULL, NULL, 'TAKENDOWN', NULL, NULL, NULL, NULL, '2026-01-13 17:29:21', NULL, NULL),
(100, 66, 1, 12, 'NDIxMjBlNmE2Y2ZmY2QzZmZiNDUyZjZiZDJmZDYyYzJiOTc2ZWMwMjgwYWQ5OTQ2NjY1ZDBjYjY2MzkyNjIwMQ==', 'OTBiNzQzNmVlYmI2NGNlZDQ5ZDJiZjZjNDMyMmQ0ZGIyY2QxY2YzNjUyNzg3OWZkYTMwNDA3YzliNDNhZThlNA==', 'PENDING', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2026-01-13 20:48:58', NULL, 5),
(101, 67, 15, 12, 'YmE3NzU0ZDI4YzJiZjJkOGJkMTlmNzZlZDE2NmEwNWVhYzA2OGY4YzhkNjQ1YzMyN2IxMWQ3OTIzZGZiNDJlZg==', 'MTc2ODM3ZjM3NWIxZWFjMTFlMTBkYjY2ZTlmNTJhMWViNTJmZDFlYzAxZjc3NGE3YzRlMTdjOTJkOTI5OGVhMA==', 'PENDING', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2026-01-13 23:15:16', NULL, 8),
(102, 68, 15, 12, 'YTg4Zjc0NWUxYzYzNzU3N2RiY2JkODRiY2I0MzRhODM3MDU1NjU2MjMzYjBhZDBjZDk3YWU4ZTViMjVhNTMxZQ==', 'NGU5MTU3M2RkOTlhNDVmNWI3NTIxOGI1ZjUyNzBiMGJhNGVjZThmNjU2OWZkOGQ1MDk3YTdhYWNkMzM3MjdiYQ==', 'PENDING', 'Deje', '1057cm', 'Koog aan de Zaan', 'Netherlands ', '2026-01-13 23:17:49', NULL, 8),
(103, 69, 12, 14, 'ZDY1OTcwMTRlNzMzMDczMTQ3MjI0ZDk0MTQzNmZiMjU3ZjQ5ZTJhNjBmYmFmNzg2MzI0MDE1ZWIwOTczMjcxZQ==', 'ZjQyZGVhYmYwNDNjNGZjZjMwZDQ1ZjkyMjBjMmYyM2M1YTIwNmUzMWZiNzU4OWIxN2U5ZGJjZDA4ZjBiNTEzNQ==', 'PENDING', 'Deje', '1057cm', 'Koog aan de Zaan', 'Netherlands ', '2026-01-16 00:09:24', NULL, 8),
(113, 79, 22, 1, 'OTAyYzU4MzBiNjkwMWQzOGYxODJlNjEyM2YzNDJjNDNlZDgwYzhjYzUzMzQwY2FkNzFlODRiOTdkZGU1YWI4Zg==', 'YTJkMjY5NzkxMjMwNjFiN2NiNmJmMjU4YmNhYzcyNGIxNDAyZGRlZTlhOGM1OGRkODhmYjk0ZDRmMmRhYTQ3MA==', 'SHIPPINGPAID', 'lel12', '1415zd', 'Amsterdam', 'Nl', '2026-01-16 22:16:23', NULL, 5),
(115, 81, 21, 12, 'ZGU0N2MwOGYyOGYyZjc3MjQ5NzNhOTllYWY5ZmY4NGFiYjM0N2FlM2RkZDFhMTA2ZDA4MGI0YTdkMDI5ODQyZA==', 'Y2I0Y2U1OThlNzU2NjRhZGQ1ZjJkY2FmNWY2YTQyZDRjYmE5ZmFhNTZjYmViYWI2NzIyZjNlNjVlOTZjNTk3NQ==', 'PENDING', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2026-01-18 18:40:25', NULL, 20),
(116, 82, 21, 12, 'ZWQ5MjllYzU0ZmNkYmMwZTQ0NTU5YTk3ZmNiYzNkNmUxN2Y4NThjNTA2ODZiNWEyZmQ4NTIxZDkwMjI3ZDI3Yw==', 'MjhmMTk5NWU2NDliNWVkZDU1MTdiNzg3MGZmNTRhN2ZkMTZmN2U0MDMzODkzZTEzN2MxOTY1ZGRlNWRiNGExZQ==', 'PENDING', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2026-01-18 18:43:05', NULL, 8),
(117, 83, 22, 14, 'Yjc4MzFmYzkzYjg1ODE1ZTc4MzFiNjkyYzY2ZGQwMjcxYmJiNTQxMWY1MjNmMmMzNjYyOWNjODVmZTlhMWZjOQ==', 'MmU3YjE3MjFhNDQ3MWFkZmYwOGRiNWNjM2Q1ZjViMTcxMTYwNzU3NDEyMTIyZmEzYzNjN2VkOWFlYjE4NjJhNQ==', 'PENDING', 'Deje', '1057cm', 'Koog aan de Zaan', 'Netherlands ', '2026-01-18 18:44:13', NULL, 8),
(118, 84, 22, NULL, NULL, NULL, 'PENDING', NULL, NULL, NULL, NULL, '2026-01-18 18:46:00', NULL, NULL),
(119, 85, 22, NULL, NULL, NULL, 'PENDING', NULL, NULL, NULL, NULL, '2026-01-18 18:54:03', NULL, NULL),
(120, 86, 12, NULL, NULL, NULL, 'TAKENDOWN', NULL, NULL, NULL, NULL, '2026-03-20 18:34:35', NULL, NULL),
(121, 87, 12, NULL, NULL, NULL, 'TAKENDOWN', NULL, NULL, NULL, NULL, '2026-03-20 18:38:34', NULL, NULL),
(122, 88, 12, NULL, NULL, NULL, 'TAKENDOWN', NULL, NULL, NULL, NULL, '2026-03-20 18:51:12', NULL, NULL),
(123, 89, 12, 1, 'YzUwNjYyYTQ4ODU1ODI5ZWE1NjQ5ZTI5M2VhM2YwYWVhOWI1NjFhYjk2NjgzMDA4ZjJkMTY2OTdjMzIzOTkxYw==', 'NTM4ZWU0MWZkNGVmOWNhNzA1NGM0MGI3YjllZjRjY2EwNjRkZTk4NDY1YjQ3NzhhNjE0ZGY5N2IyZDUzOTJhNQ==', 'PENDING', 'lel12', '1415zd', 'Amsterdam', 'Nl', '2026-03-27 14:25:44', NULL, 20),
(124, 90, 1, 12, 'YThjZDRlMTZhNzA3MjFiYWI5YjZkZDAyNGRmMWQxY2I3M2RlMjFkYmMwMzg2OGNiZDk4MTk0Y2NlM2VmYWZmMw==', 'MDAzNDA2YTMzNWY0NTk1MDU5YzgxZGJmODdkNmYxYjJhZjlmMmU4MWUyZmYxZTBjYzkwNGEyZWE3MTdhYzUwMQ==', 'COMPLETED', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2026-03-30 22:33:31', '2026-03-30 22:37:08', 5),
(125, 91, 1, 12, 'NThkZjE3NzEzZTI1ZDNhZTI4OGY2YTY5MWE4OGZjODQ1YTYwMDQxNjRmOGYwNjBkZDZjODdmMTY0YjNkZWUyNg==', 'MDhlOWIzNTdiNTY4MzU5MjQ1NTk5YzVhODI5MjJjNzlkMGY1OWU1YTZlZjIzZjA4ZDQ1ZGRiYjM1YjEwNTcwOA==', 'PENDING', 'Oeverpad 382', '1068PL', 'Koog aan de Zaan', 'Netherlands', '2026-03-30 22:38:36', NULL, 8);

-- --------------------------------------------------------

--
-- Table structure for table `direct_messages`
--

CREATE TABLE `direct_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `direct_messages`
--

INSERT INTO `direct_messages` (`id`, `sender_id`, `recipient_id`, `message`, `is_read`, `created_at`) VALUES
(1, 12, 14, 'Yo', 0, '2026-03-06 23:45:25'),
(2, 12, 14, 'asd', 0, '2026-03-06 23:52:26'),
(3, 12, 21, '213', 0, '2026-03-06 23:53:32'),
(4, 14, 12, 'sadasdasd', 0, '2026-03-06 23:52:26'),
(5, 12, 21, 'asd', 0, '2026-03-06 23:58:56'),
(6, 12, 21, 'asd', 0, '2026-03-07 00:06:51'),
(7, 12, 21, 'fffff', 0, '2026-03-07 00:06:58'),
(8, 1, 1, 'asd', 0, '2026-03-07 00:13:44'),
(9, 1, 1, 'hahah\n', 0, '2026-03-07 00:14:02'),
(10, 1, 1, ':D', 0, '2026-03-07 00:14:21'),
(11, 1, 1, 'asd', 0, '2026-03-07 00:23:59'),
(12, 12, 1, 'Yo', 0, '2026-03-07 00:25:20'),
(13, 12, 1, 'asdasd', 0, '2026-03-07 00:27:06'),
(14, 12, 1, 'asdasd', 0, '2026-03-07 00:27:50'),
(15, 12, 1, 'asdasd', 0, '2026-03-07 00:29:26'),
(16, 12, 1, 'ASDASDASasd', 0, '2026-03-07 00:30:41'),
(17, 12, 1, 'pedal', 0, '2026-03-07 00:32:09'),
(18, 1, 12, 'dsad', 0, '2026-03-07 00:32:30'),
(19, 12, 1, 'pedal', 0, '2026-03-07 00:34:28'),
(20, 12, 1, '12', 0, '2026-03-20 15:42:25'),
(21, 12, 1, 'pedal\n', 0, '2026-03-20 15:42:34'),
(22, 12, 14, '11', 0, '2026-03-20 17:12:47'),
(23, 12, 1, '1', 0, '2026-03-20 21:34:40'),
(24, 12, 1, '65', 0, '2026-03-20 21:34:48'),
(25, 12, 1, 'zxcasd', 0, '2026-03-20 21:34:53'),
(26, 12, 1, '221323', 0, '2026-03-20 21:43:38'),
(27, 12, 1, '321', 0, '2026-03-20 21:43:42'),
(28, 12, 1, '123', 0, '2026-03-20 21:44:39'),
(29, 12, 1, '123', 0, '2026-03-20 21:48:54'),
(30, 12, 1, '3321', 0, '2026-03-20 21:48:59'),
(31, 12, 1, 'kur\n', 0, '2026-03-20 21:49:10'),
(32, 1, 12, '123123', 0, '2026-03-20 21:49:15'),
(33, 1, 12, 'pedal', 0, '2026-03-20 21:49:32'),
(34, 12, 1, '123', 0, '2026-03-20 21:57:43'),
(35, 12, 1, '123', 0, '2026-03-20 21:57:47'),
(36, 12, 1, '123', 0, '2026-03-20 21:57:52'),
(37, 12, 1, '123', 0, '2026-03-20 21:57:57'),
(38, 12, 1, 'ypoyuu', 0, '2026-03-20 21:58:24'),
(39, 12, 1, '12', 0, '2026-03-20 21:59:39'),
(40, 12, 1, 'haha', 0, '2026-03-20 21:59:46'),
(41, 12, 15, 'Eyyyo\n', 0, '2026-03-20 22:10:50'),
(42, 15, 12, 'Heka', 0, '2026-03-20 22:11:08'),
(44, 12, 12, '12', 0, '2026-03-26 15:48:08'),
(45, 12, 1, '3', 0, '2026-03-26 15:48:16'),
(46, 12, 1, '4', 0, '2026-03-26 15:48:25'),
(47, 12, 1, '3', 0, '2026-03-26 15:50:50'),
(48, 1, 12, '3', 0, '2026-03-26 15:51:47'),
(49, 12, 1, '3', 0, '2026-03-26 15:51:55'),
(50, 1, 12, '3', 0, '2026-03-26 15:52:16'),
(51, 1, 12, '5555', 0, '2026-03-26 15:52:22'),
(52, 1, 12, '3', 0, '2026-03-26 15:52:42'),
(53, 1, 12, '1', 0, '2026-03-26 16:08:25'),
(54, 1, 12, '1', 0, '2026-03-26 16:13:24'),
(55, 12, 14, '2', 0, '2026-03-26 16:42:02'),
(56, 1, 1, '1', 0, '2026-03-26 16:43:57'),
(57, 12, 1, 'gaasda', 0, '2026-03-26 16:45:15'),
(58, 12, 1, '1', 0, '2026-03-26 17:15:21'),
(59, 1, 12, '21\n', 0, '2026-03-26 17:15:44'),
(60, 12, 1, 'wsdfs', 0, '2026-03-26 17:18:07'),
(61, 1, 12, '123', 0, '2026-03-26 17:18:22'),
(62, 12, 1, '123', 0, '2026-03-26 17:18:31'),
(63, 12, 1, 'qw', 0, '2026-03-26 17:21:08'),
(64, 12, 1, '21', 0, '2026-03-26 17:21:17'),
(65, 12, 1, '12', 0, '2026-03-26 17:21:25'),
(66, 12, 1, '12', 0, '2026-03-26 17:23:33'),
(67, 12, 1, 'haha\n', 0, '2026-03-26 17:23:43'),
(68, 1, 12, '123', 0, '2026-03-26 17:27:36'),
(69, 1, 12, '312', 0, '2026-03-26 17:27:50'),
(70, 12, 14, 'qwwe', 0, '2026-03-26 23:14:14'),
(71, 12, 22, '12', 0, '2026-03-26 23:25:19'),
(72, 12, 1, 'asdfasd', 0, '2026-03-26 23:25:58'),
(73, 12, 14, 'asd', 0, '2026-03-27 00:08:56'),
(74, 12, 14, 'gggasdasd', 0, '2026-03-27 00:09:27'),
(75, 12, 14, 'asd', 0, '2026-03-27 00:09:41'),
(76, 12, 15, '123', 0, '2026-03-27 00:25:52'),
(77, 12, 15, '444422', 0, '2026-03-27 00:25:57'),
(78, 12, 14, '444', 0, '2026-03-27 00:26:13'),
(79, 12, 15, '231', 0, '2026-03-27 00:30:03'),
(80, 12, 15, '123', 0, '2026-03-27 00:30:13'),
(81, 12, 15, '123123', 0, '2026-03-27 00:30:40'),
(82, 12, 1, 'kk', 0, '2026-03-27 00:30:58'),
(83, 12, 14, '123', 0, '2026-03-27 00:31:21'),
(84, 12, 1, '123', 0, '2026-03-27 00:31:51'),
(85, 12, 1, '+52', 0, '2026-03-27 00:32:38'),
(86, 12, 1, '12', 0, '2026-03-27 00:35:28'),
(87, 12, 1, '123', 0, '2026-03-27 00:35:41'),
(88, 12, 1, '123', 0, '2026-03-27 00:37:28'),
(89, 12, 1, '1233333', 0, '2026-03-27 00:42:02'),
(90, 12, 1, '5456', 0, '2026-03-27 00:46:56'),
(91, 12, 1, '.', 0, '2026-03-27 00:47:35'),
(92, 12, 22, '12', 0, '2026-03-27 00:50:52'),
(93, 12, 15, '123', 0, '2026-03-27 00:51:13'),
(94, 12, 14, '123', 0, '2026-03-27 00:52:30'),
(95, 12, 14, '123', 0, '2026-03-27 00:52:54'),
(96, 12, 14, '123', 0, '2026-03-27 00:56:58'),
(97, 1, 12, '23', 0, '2026-03-27 00:57:39'),
(98, 1, 12, '123', 0, '2026-03-27 00:57:49'),
(99, 12, 14, '21`12', 0, '2026-03-27 00:59:18'),
(100, 12, 14, '`12', 0, '2026-03-27 00:59:34'),
(101, 12, 14, 'adasdasdasdasd', 0, '2026-03-27 00:59:47'),
(102, 12, 14, '123', 0, '2026-03-27 01:00:00'),
(103, 12, 14, '1233333', 0, '2026-03-27 01:01:05'),
(104, 12, 14, '331', 0, '2026-03-27 01:01:22'),
(105, 12, 1, '321', 0, '2026-03-27 01:03:15'),
(106, 12, 1, '2', 0, '2026-03-27 01:03:22'),
(107, 12, 12, '123', 0, '2026-03-27 01:03:46'),
(108, 12, 12, '123', 0, '2026-03-27 01:04:17'),
(109, 12, 12, '213', 0, '2026-03-27 01:04:24'),
(110, 12, 12, '123', 0, '2026-03-27 01:04:30'),
(111, 12, 14, '123', 0, '2026-03-27 01:04:55'),
(112, 12, 14, '123', 0, '2026-03-27 01:05:47'),
(113, 12, 14, '123', 0, '2026-03-27 01:05:56'),
(114, 12, 14, '123', 0, '2026-03-27 01:06:01'),
(115, 12, 14, '42123', 0, '2026-03-27 01:06:07'),
(116, 12, 14, '123', 0, '2026-03-27 01:06:13'),
(117, 12, 14, 'asdfasd', 0, '2026-03-27 01:06:17'),
(118, 12, 14, '23', 0, '2026-03-27 01:07:18'),
(119, 12, 14, '123', 0, '2026-03-27 01:07:31'),
(120, 12, 1, '123', 0, '2026-03-27 01:10:01'),
(121, 12, 1, '123', 0, '2026-03-27 01:10:06'),
(122, 12, 1, '123', 0, '2026-03-27 01:12:35'),
(123, 12, 14, '123', 0, '2026-03-27 01:12:39'),
(124, 12, 14, '4444', 0, '2026-03-27 01:12:42'),
(125, 12, 21, '333', 0, '2026-03-27 01:12:46'),
(126, 12, 22, '123123', 0, '2026-03-27 01:15:20'),
(127, 12, 14, '123333', 0, '2026-03-27 01:16:01'),
(128, 1, 12, '12334asdasd', 0, '2026-03-27 01:16:14'),
(129, 12, 1, '123', 0, '2026-03-27 01:16:29'),
(130, 12, 1, 'asd', 0, '2026-03-27 01:16:35'),
(131, 12, 1, 'dflal;sdf', 0, '2026-03-27 09:20:39'),
(132, 12, 1, 'Hellooo', 0, '2026-03-27 09:20:54'),
(133, 1, 12, 'asdkasd', 0, '2026-03-27 09:20:58'),
(134, 12, 14, 'sadasdas', 0, '2026-03-27 10:50:02'),
(135, 1, 12, 'aSads', 0, '2026-03-27 10:50:18'),
(136, 1, 12, 'asdasdasd', 0, '2026-03-27 10:50:23'),
(137, 1, 12, 'Hello', 0, '2026-03-27 10:50:35'),
(138, 12, 1, 'sdasd', 0, '2026-03-27 12:06:03'),
(139, 1, 14, 'ASDASD', 0, '2026-03-27 12:09:43'),
(140, 12, 1, 'asd', 0, '2026-03-27 14:23:08'),
(141, 12, 14, 'asd', 0, '2026-03-27 14:23:14'),
(142, 12, 1, 'hgfghf', 0, '2026-03-27 14:28:05'),
(143, 1, 12, 'kjghhkg', 0, '2026-03-27 14:28:09'),
(144, 12, 1, 'pedal', 0, '2026-03-27 22:42:47'),
(145, 12, 1, '2', 0, '2026-03-27 23:00:06'),
(146, 12, 21, ',km', 0, '2026-03-27 23:00:10'),
(147, 12, 1, 'k', 0, '2026-03-27 23:00:54'),
(148, 12, 1, '12', 0, '2026-03-27 23:01:58'),
(149, 12, 1, '22', 0, '2026-03-27 23:04:01'),
(150, 1, 12, '12', 0, '2026-03-27 23:04:06'),
(151, 12, 1, '2', 0, '2026-03-27 23:04:10'),
(152, 12, 1, '2', 0, '2026-03-27 23:06:30'),
(153, 12, 1, '3', 0, '2026-03-27 23:06:35'),
(154, 12, 1, '12', 0, '2026-03-27 23:06:42'),
(155, 12, 1, '12', 0, '2026-03-27 23:06:46'),
(156, 12, 1, 'adsasdasdasd', 0, '2026-03-27 23:06:49'),
(157, 12, 1, 'asdasd', 0, '2026-03-27 23:06:58'),
(158, 12, 1, 'asdasdasd', 0, '2026-03-27 23:07:07'),
(159, 12, 1, 'asdasd', 0, '2026-03-27 23:07:15'),
(160, 12, 1, 'asdasd', 0, '2026-03-27 23:07:23'),
(161, 12, 1, 'fakakaka', 0, '2026-03-27 23:07:31'),
(162, 12, 1, 'asdasd', 0, '2026-03-27 23:07:53'),
(163, 12, 1, '123', 0, '2026-03-27 23:08:22'),
(164, 12, 1, 'asdasddd', 0, '2026-03-27 23:08:33'),
(165, 12, 1, 'asd', 0, '2026-03-27 23:08:53'),
(166, 1, 12, '2123', 0, '2026-03-27 23:09:00'),
(167, 12, 21, 'asd', 0, '2026-03-27 23:13:08'),
(168, 12, 1, 'sdasd', 0, '2026-03-27 23:13:14'),
(169, 12, 1, '565456', 0, '2026-03-30 14:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(20) NOT NULL,
  `lname` varchar(20) NOT NULL,
  `role` enum('ADMIN','USER','GUEST') NOT NULL DEFAULT 'GUEST',
  `email` varchar(60) NOT NULL,
  `password_hash` longtext NOT NULL,
  `address` varchar(30) DEFAULT NULL,
  `post_code` varchar(10) DEFAULT NULL,
  `country` varchar(25) DEFAULT NULL,
  `joined_at` timestamp NULL DEFAULT current_timestamp(),
  `resset_token` longtext DEFAULT NULL,
  `resset_token_expiry` datetime DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `isVerified` tinyint(1) DEFAULT NULL,
  `swap_tokens` int(11) NOT NULL DEFAULT 0,
  `state` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `role`, `email`, `password_hash`, `address`, `post_code`, `country`, `joined_at`, `resset_token`, `resset_token_expiry`, `isActive`, `isVerified`, `swap_tokens`, `state`) VALUES
(1, 'Paami', 'Savov', 'ADMIN', 'paami97@gmail.com', '$2y$12$ZvMgJFKl8A3MOraul2S4mebyI9/VBvgw9gja/cen34XrqdTGW0VA2', 'lel12', '1415zd', 'Nl', '2025-11-20 16:33:08', 'NDkwOTkwYTg3NDA2ODJmMGQ3ZDM3YjllYTMzMzk3ZjUxM2U1ZWY5NDI2OTU5YzRhOTJkZGQxNGZlNjJkZDgwZg==', '2026-02-21 21:52:09', 1, 0, 9, 'Amsterdam'),
(12, 'Milko', 'Savov', 'USER', 'milensavov1997@gmail.com', '$2y$12$SoVajkEYeCINMxEU29H63.7jOO6kplg3OL3i4Y4tM92g8RB5iQNZS', 'Oeverpad 382', '1068PL', 'Netherlands', '2025-12-16 20:57:04', NULL, NULL, 1, 0, 9, 'Koog aan de Zaan'),
(14, 'Milen', 'Savov', 'USER', '580601@student.inholland.nl', '$2y$12$ugjzwpj49HGJVbfu919i3uY0VQPB48NBM3b3K8V2rf3NpldYllJ8e', 'Deje', '1057cm', 'Netherlands ', '2025-12-17 17:40:38', NULL, NULL, 1, 0, 1, 'Koog aan de Zaan'),
(15, 'Vankata', 'Ivanov', 'USER', 'ligawow@abv.bg', '$2y$12$cFZsJa24Q2mwhyal5cusl.qdrKsG9VGqqXtQropGmwuhM2jcMpjwe', 'Oeverpad 382', '1068PL', 'Netherlands', '2026-01-13 23:10:27', NULL, NULL, 1, 0, 0, 'Amsterdam'),
(16, 'Test', 'User', 'USER', 'test@example.com', '$2y$12$FsAAvBkL09GIg8u6anrg2uHrY/ifNXwIp9Lt8ONUQa8l5u.wRL34C', 'Oeverpad 382', '1068PL', 'Netherlands', '2026-01-13 23:43:28', NULL, NULL, 1, 0, 0, 'amsterdam'),
(20, 'User', 'Testov', 'USER', 'User@example.mail', '$2y$12$xZcNG7MLNJQT7tp4C4Elq.hDnT6b8BlCxGkweR2WKUF0raygSVku6', 'Oeverpad 382', '1068PL', 'Netherlands', '2026-01-14 00:20:57', NULL, NULL, 1, 0, 0, 'amsterdam'),
(21, 'Admin', 'Testing', 'USER', 'test@admin.mail', '$2y$12$QC5U58LzcW6Qf5PXm9YHQO9QkTuDZLvfGMZOMLMa5CRkMPhWYpUPi', 'Bijdorplaan 15', '2015CE ', 'Netherlands', '2026-01-16 21:27:37', NULL, NULL, 1, 0, 1, 'Haarlem'),
(22, 'User', 'Testing', 'USER', 'test@user.mail', '$2y$12$cwH.GlrFzcHQZ3GZnX3hC.nK/yHjxdg0B1DLVU7U7aft3uj4H8WTS', 'Bijdorplaan 15', '2015CE ', 'Netherlands', '2026-01-16 21:29:51', NULL, NULL, 1, 0, 1, 'Haarlem'),
(23, 'Asan', 'Keksa', 'USER', 'asan@test.com', '$2y$12$SdQmDY0Y0c8BXwhnOxBoS.CfEmPId9Dt6qqB7TUWWp6v/KKb/9wQy', 'Oeverpad 382', '1068PL', 'Netherlands', '2026-01-18 16:16:05', NULL, NULL, 1, 0, 0, 'amsterdam'),
(29, 'Milen', 'Savov', 'USER', 'milensav213ov1997@gmail.com', '$2y$12$xXJB.jIMMZHU4qPxNiXjo.gydKEEChF5L4FAQ.EKlTiu6J.9qNeM.', 'Oeverpad 382', '1068PL', 'Netherlands', '2026-02-23 13:49:48', NULL, NULL, 1, 0, 0, 'amsterdam'),
(30, 'Milen', 'Savov', 'USER', 'milensavov1997@gmail.com123', '$2y$12$a1VXFWX/FB25B.pEX6gKtOzBx84uBX4wkc7w/plg/7jMntbw0zy.y', 'Oeverpad 382', '1068PL', 'Netherlands', '2026-03-20 10:41:50', NULL, NULL, 1, 0, 0, 'amsterdam'),
(31, 'Milen', 'Savov', 'USER', 'milensavov1997@gmail.com55698', '$2y$12$gDEKi0XC0hd3fFIuxRDhQey6/9wpcbBXdN46tB5TUmAF9qTW16NeS', 'Oeverpad 382', '1068PL', 'Netherlands', '2026-03-20 22:39:07', NULL, NULL, 1, 0, 0, 'amsterdam'),
(32, 'Milen', 'Savov', 'USER', 'milensavov1997@gmail.com13123asda', '$2y$12$fKtHw6kMfi6zhkK7zeEXfO7zMP8cQHDp0CtfrIJPrhvD7lsq.lLp2', 'Oeverpad 382', '1068PL', 'Netherlands', '2026-03-30 23:23:26', NULL, NULL, 1, 0, 0, 'amsterdam'),
(33, 'Milen', 'Savov', 'USER', 'milensavov1997@gmail.com123123', '$2y$12$zw9OdcnjxoiRqKcUzdlty.UUm/fdkJ/Zpy66u2zety99/Ri14xuEq', 'Oeverpad 382', '1068PL', 'Netherlands', '2026-03-30 23:43:55', NULL, NULL, 1, 0, 0, 'amsterdam'),
(34, 'Milen', 'Savov', 'USER', 'milensavov1997@gmail.comasda123123', '$2y$12$2gS8VuJXuIyJrQEE/S5OiOkUyMD/Fs8WNfAgPOtaE8Q1px0tEOmza', 'Oeverpad 382', '1068PL', 'Netherlands', '2026-03-30 23:58:43', NULL, NULL, 1, 0, 0, 'amsterdam'),
(35, 'Milen', 'Savov', 'USER', 'milensavov1997@gmail.comadad11222', '$2y$12$zOdkPd2gzMlj5rlbA8l.TenhmU98NHxZ10YfOC1Hk.uAJODkQ.bAG', 'Oeverpad 382', '1068PL', 'Netherlands', '2026-03-31 00:49:28', NULL, NULL, 1, 0, 0, 'amsterdam');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shared_by` (`shared_by`);

--
-- Indexes for table `book_swap_requests`
--
ALTER TABLE `book_swap_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_owner_action_token` (`owner_action_token`) USING HASH,
  ADD UNIQUE KEY `uniq_requester_action_token` (`requester_action_token`) USING HASH,
  ADD KEY `idx_book_id` (`book_id`),
  ADD KEY `idx_owner_id` (`owner_id`),
  ADD KEY `idx_requester_id` (`requester_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `direct_messages`
--
ALTER TABLE `direct_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipient_id` (`recipient_id`),
  ADD KEY `idx_conversation` (`sender_id`,`recipient_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `book_swap_requests`
--
ALTER TABLE `book_swap_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `direct_messages`
--
ALTER TABLE `direct_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`shared_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`shared_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `book_swap_requests`
--
ALTER TABLE `book_swap_requests`
  ADD CONSTRAINT `bsr_book_fk` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bsr_owner_fk` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bsr_requester_fk` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `direct_messages`
--
ALTER TABLE `direct_messages`
  ADD CONSTRAINT `direct_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `direct_messages_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
