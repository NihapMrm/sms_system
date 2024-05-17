-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 01, 2024 at 12:35 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sms`
--

-- --------------------------------------------------------

--
-- Table structure for table `Admins`
--

CREATE TABLE `Admins` (
  `AdminID` int(11) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `PasswordHash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Admins`
--

INSERT INTO `Admins` (`AdminID`, `FirstName`, `LastName`, `Email`, `PasswordHash`) VALUES
(1, 'Nihap', 'Mrm', 'nihapmrm@gmail.com', '$2a$12$TJqYzjvdAfI2AZKn.cILuehksQ3j3aXspKwGmpBqInj4JaeQhiGG.');

-- --------------------------------------------------------

--
-- Table structure for table `Attendance`
--

CREATE TABLE `Attendance` (
  `RecordID` int(11) NOT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `Status` enum('present','absent') DEFAULT NULL,
  `CourseID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Attendance`
--

INSERT INTO `Attendance` (`RecordID`, `StudentID`, `Date`, `Status`, `CourseID`) VALUES
(1, 34, '2023-12-18', 'present', 1),
(2, 36, '2023-12-18', 'absent', 1),
(3, 35, '2023-12-30', 'present', 3),
(4, 37, '2023-12-30', 'absent', 3);

-- --------------------------------------------------------

--
-- Table structure for table `Certificates`
--

CREATE TABLE `Certificates` (
  `CertificateID` int(11) NOT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `CourseID` int(11) DEFAULT NULL,
  `IssueDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CourseModules`
--

CREATE TABLE `CourseModules` (
  `ModuleID` int(11) NOT NULL,
  `CourseID` int(11) DEFAULT NULL,
  `ModuleName` varchar(100) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `PDFPath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Courses`
--

CREATE TABLE `Courses` (
  `CourseID` int(11) NOT NULL,
  `CourseName` varchar(100) DEFAULT NULL,
  `DurationInMonths` int(11) DEFAULT NULL,
  `CourseFee` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Courses`
--

INSERT INTO `Courses` (`CourseID`, `CourseName`, `DurationInMonths`, `CourseFee`) VALUES
(1, 'Web Developer', 3, '20000'),
(2, 'ICT', 4, '10000'),
(3, 'Software Engineering', 6, '120000'),
(4, 'Test', 12, '200000'),
(5, 'Software Developer', 24, '1200000'),
(6, 'English Literature', 48, '20000'),
(7, 'Training', 1, '1500');

-- --------------------------------------------------------

--
-- Table structure for table `Payments`
--

CREATE TABLE `Payments` (
  `PaymentID` int(11) NOT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `DatePaid` date DEFAULT current_timestamp(),
  `PaymentStatus` enum('pending','completed') DEFAULT NULL,
  `CourseId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Payments`
--

INSERT INTO `Payments` (`PaymentID`, `StudentID`, `Amount`, `DatePaid`, `PaymentStatus`, `CourseId`) VALUES
(2, 34, 20000.00, '2023-12-24', 'completed', NULL),
(3, 35, 12000.00, '2023-12-25', 'completed', NULL),
(4, 36, 3000.00, '2023-12-25', 'completed', 1),
(5, 34, 10000.00, '2023-12-25', 'completed', 2),
(6, 35, 1200000.00, '2023-12-25', 'completed', 5),
(7, 34, 10000.00, '2023-12-25', 'completed', 1),
(8, 34, 10000.00, '2023-12-25', 'completed', 1),
(9, 37, 120000.00, '2023-12-28', 'completed', 3),
(10, 38, 100.00, '2023-12-30', 'pending', 1),
(11, 39, 20000.00, '2023-12-30', 'completed', 6),
(12, 40, 5000.00, '2024-01-01', 'pending', 3),
(13, 40, 100000.00, '2024-01-01', 'completed', 3),
(14, 40, 1000.00, '2024-01-01', 'completed', 3),
(15, 40, 0.00, '2024-01-01', 'completed', 3);

-- --------------------------------------------------------

--
-- Table structure for table `Registrations`
--

CREATE TABLE `Registrations` (
  `RegistrationID` int(11) NOT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `CourseID` int(11) DEFAULT NULL,
  `RegistrationDate` date DEFAULT NULL,
  `Status` enum('enrolled','completed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `StudentCourses`
--

CREATE TABLE `StudentCourses` (
  `StudentID` int(11) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `EnrollmentDate` date DEFAULT NULL,
  `CompletionDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `StudentCourses`
--

INSERT INTO `StudentCourses` (`StudentID`, `CourseID`, `EnrollmentDate`, `CompletionDate`) VALUES
(34, 1, '2023-12-24', NULL),
(34, 2, '2023-12-25', '2024-04-25'),
(35, 3, '2023-12-25', '2024-06-25'),
(35, 5, '2023-12-25', '2025-12-25'),
(36, 1, '2023-12-25', '2024-03-25'),
(37, 3, '2023-12-28', '2024-06-28'),
(38, 1, '2023-12-30', '2024-03-30'),
(39, 6, '2023-12-30', '2027-12-30'),
(40, 3, '2024-01-01', '2024-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `Students`
--

CREATE TABLE `Students` (
  `StudentID` int(11) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL,
  `PasswordHash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Students`
--

INSERT INTO `Students` (`StudentID`, `FirstName`, `LastName`, `Email`, `Status`, `PasswordHash`) VALUES
(23, 'sdfsdf', 'dssdf', 'testmail.com', 'Registered', '$2y$10$abNxf/nuLdgFiwhf3F6ftOqqwL/Ih/6HHigysKtB/HAC64q2gGAO6'),
(24, 'test', 'test', 'reyah42816@wenkuu.com', 'Registered', '$2y$10$w/uAbIJg8Gin2x/1roQ6ze2zTyXMJ7qegy6qHr6o496dknot83aNK'),
(34, 'nihap', 'mrm', 'nihapmrm@gmail.com', 'Registered', '$2y$10$5g56I9ZAP6hsdDxLZwgfSuoZ8uwWX7O7xIN2fCe0R3tSdvViUMOs.'),
(35, 'test2', 'asda', 'sdsse@mail.com', 'Registered', '$2y$10$ULVInzotetGW9wEIEiqmceYhuR10KSu5uDwemScgZI89HY29yp5hO'),
(36, 'werw', 'ertet', 'testmawe@wil.com', 'Registered', '$2y$10$qL1D0hvQXZCkK2WIorPvSePlv6RciKV9wC8AGHV6VjqHpGxTLT2Am'),
(37, 'kumar', 'raj', 'rjkumar@mail.com', 'Registered', '$2y$10$JWlphfz3MJC.5VvKpV3Nk.T/JId4I4cx5.DwIrrhkHVzGkC9wSHBu'),
(38, 'Test', 'Test 1', 'test1@mail.com', 'Registered', '$2y$10$yKdN/1AziFr57EQGkMamr.pmbObeCUoijZuult/NZIeZrfaw83DGS'),
(39, 'Test', 'Test 2', 'test2@mail.com', 'Registered', '$2y$10$ZNuszpL/nZMO3FwCZXmX8evLV5jmCuSKF3KilKs1O5Z2sxNAa2NJ2'),
(40, 'Sihap', 'Mrm', 'sihap@gmail.com', 'Registered', '$2y$10$t.aBdH7CqL5vZQvVgldUtuemG.Lacc353OdkKX89iVpNaVubJ3l9O');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Admins`
--
ALTER TABLE `Admins`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `Attendance`
--
ALTER TABLE `Attendance`
  ADD PRIMARY KEY (`RecordID`),
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `Certificates`
--
ALTER TABLE `Certificates`
  ADD PRIMARY KEY (`CertificateID`),
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `CourseModules`
--
ALTER TABLE `CourseModules`
  ADD PRIMARY KEY (`ModuleID`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `Courses`
--
ALTER TABLE `Courses`
  ADD PRIMARY KEY (`CourseID`);

--
-- Indexes for table `Payments`
--
ALTER TABLE `Payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `FK_CourseId` (`CourseId`);

--
-- Indexes for table `Registrations`
--
ALTER TABLE `Registrations`
  ADD PRIMARY KEY (`RegistrationID`),
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `StudentCourses`
--
ALTER TABLE `StudentCourses`
  ADD PRIMARY KEY (`StudentID`,`CourseID`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `Students`
--
ALTER TABLE `Students`
  ADD PRIMARY KEY (`StudentID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Admins`
--
ALTER TABLE `Admins`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Attendance`
--
ALTER TABLE `Attendance`
  MODIFY `RecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Certificates`
--
ALTER TABLE `Certificates`
  MODIFY `CertificateID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `CourseModules`
--
ALTER TABLE `CourseModules`
  MODIFY `ModuleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Courses`
--
ALTER TABLE `Courses`
  MODIFY `CourseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Payments`
--
ALTER TABLE `Payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `Registrations`
--
ALTER TABLE `Registrations`
  MODIFY `RegistrationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Students`
--
ALTER TABLE `Students`
  MODIFY `StudentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Attendance`
--
ALTER TABLE `Attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `Students` (`StudentID`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `Courses` (`CourseID`);

--
-- Constraints for table `Certificates`
--
ALTER TABLE `Certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `Students` (`StudentID`),
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `Courses` (`CourseID`);

--
-- Constraints for table `CourseModules`
--
ALTER TABLE `CourseModules`
  ADD CONSTRAINT `coursemodules_ibfk_1` FOREIGN KEY (`CourseID`) REFERENCES `Courses` (`CourseID`);

--
-- Constraints for table `Payments`
--
ALTER TABLE `Payments`
  ADD CONSTRAINT `FK_CourseId` FOREIGN KEY (`CourseId`) REFERENCES `Courses` (`CourseID`),
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `Students` (`StudentID`);

--
-- Constraints for table `Registrations`
--
ALTER TABLE `Registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `Students` (`StudentID`),
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `Courses` (`CourseID`);

--
-- Constraints for table `StudentCourses`
--
ALTER TABLE `StudentCourses`
  ADD CONSTRAINT `studentcourses_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `Students` (`StudentID`),
  ADD CONSTRAINT `studentcourses_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `Courses` (`CourseID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
