USE greeliving;

DESCRIBE TABLE InPersonInterview;

INSERT INTO Applicant (AuthenticationID, FirstName, LastName, Birthdate, Gender, Email, Phone) VALUES ('auth0|1234123123', 'Pine', 'Ta', '2004-09-21', 'Male', 'pinetar@gmail.com', '1234567890');
SELECT * FROM Applicant;

INSERT INTO Company (AuthenticationID, CompanyName, CompanySize, Phone, Email, Introduction) VALUES ('auth0|abcdefghiklmn', 'Pine Computerworks', '100+ employees', '1234657890', 'pinecomputerworks@gmail.com', 'We make computers!');
SELECT * FROM Company;

INSERT INTO Specialization (SpecializationName) VALUE ("Computer engineering");
SELECT * FROM Specialization;

INSERT INTO Job (CompanyID, JobTitle, ApplicationDeadline, Salary, WorkingLocation, SpecializationID, ExperienceRequirement, WorkingFormat, ScopeOfWork, Benefits) VALUES (1, "Computer Builder", "2023-11-07 23:59:59", "$200/hour", "Hanoi", 1, "Senior", "On-site", "Build computers, get moni.", "Monei.");
SELECT * FROM Job;

INSERT INTO JobApplication (JobID, ApplicantID, TimeOfApplication, CV, StatementOfPurpose, ExpectToGain, Questions, ApplicationStatus) VALUES (1, 1, "2023-11-07 23:59:59", "Some CV i guess", "I need money to get rich.", "Cash, mint, dime, benjamins.", "Does this job pay well?", "Interviewing");
SELECT * FROM JobApplication;

INSERT INTO JobApplication (JobID, ApplicantID, TimeOfApplication, CV, StatementOfPurpose, ExpectToGain, Questions, ApplicationStatus) VALUES (1, 1, "2023-11-07 23:59:49", "Some CV i guess", "I need money to get rich but plz interview online.", "Cash, mint, dime, benjamins.", "Does this job pay well?", "Interviewing");

INSERT INTO InterviewType (`InterviewType`) VALUES ("in-person"), ("on-the-go");
SELECT * FROM InterviewType;

INSERT INTO Interview (ApplicationID, InterviewTypeID) VALUES (1, 1);
SELECT * FROM Interview;
INSERT INTO Interview (ApplicationID, InterviewTypeID) VALUES (2, 2);

INSERT INTO InPersonInterview (ApplicationID) VALUES (1);
SELECT * FROM InPersonInterview;

INSERT INTO InPersonInterviewDate (ApplicationID, InterviewTimeFrom, InterviewTimeTo) VALUES
('1', '2023-11-08 15:00:00', '2023-11-08 16:00:00'),
('1', '2023-11-08 16:00:00', '2023-11-08 17:00:00'),
('1', '2023-11-08 17:00:00', '2023-11-08 18:00:00');
SELECT * FROM InPersonInterviewDate;

INSERT INTO OnTheGoInterview (ApplicationID, InterviewTimeFrom, InterviewTimeTo, InterviewLink) VALUES
('2', "2023-11-09 17:00:00", "2023-11-09 19:00:00", "https://facebook.com");

-- For a given application of an applicant that has been scheduled for an IN PERSON interview, list all interview dates scheduled by the employer?
-- Known: The interview for this application exists in the interview and in-person interview table.
-- The interview is in person. ApplicationID = 2, (nah ApplicantID = 1).
SELECT InPersonInterview.ApplicationID, InterviewTimeFrom, InterviewTimeTo, Booked FROM Interview
JOIN InPersonInterview ON Interview.ApplicationID = InPersonInterview.ApplicationID
JOIN InPersonInterviewDate ON InPersonInterview.ApplicationID = InPersonInterviewDate.ApplicationID
WHERE InPersonInterview.ApplicationID = 1;

-- Get all applications of an applicant
SELECT * FROM JobApplication
JOIN Job ON JobApplication.JobID = Job.JobID
WHERE ApplicantID = 1;

-- How the profile page will be created:
-- 1. Get all the applications of the applicant. Default: Limit to within the last 30 days. Options to limit within 100 days, 1 year, or all time.
-- Jobs are ordered by status: Interviewing -> Reviewing -> Applying -> Succeeded -> Failed. WE WILL SHOW SAVED JOBS IN A DIFFERENT SECTION FOR CLARITY.
SELECT * FROM JobApplication WHERE ApplicantID = '1' AND ApplicationStatus != 'Saved' ORDER BY ApplicationStatus;
-- 2. Each application is presented as a card containing a key details of that application (job title, company, status) and a link to a detailed page containing other information such as CV and stuff.
-- 3. For each application marked as interviewing, display the interview information (see below). We run this query for each application marked as interviewing. We know its ID beforehand.
SELECT * FROM Interview
LEFT JOIN InPersonInterview ON Interview.InterviewID = InPersonInterview.InterviewID
LEFT JOIN OnTheGoInterview ON Interview.InterviewID = OnTheGoInterview.InterviewID
WHERE ApplicationID = '1';

-- Use cases (interview-wise):
-- Applicants: For all applications marked as "interviewing":
-- - The employer will decide the format of the interview (i.e. in person or on the go)
-- - If the employer has not decided the format, show "Awaiting details".
-- - If the employer has decided that the interview is in person, link to a separate containing the dates the employer has chosen so that the applicant can pick a date.
-- - If the interview is in person and the applicant has fixed the date, the user will see the confirmed date.
-- - If the employer has decided that the interview is online, show the applicant the date and time of the interview. Also leave them a link to another page containing the details of the meeting.
-- Employers:
-- - The employer can change the status of an application to "interviewing".
-- - For applications marked as "interviewing", the employer will see a link to another page where they can choose the interview format and arrange dates/links.
-- - For in-person interviews, employers can add or remove dates as needed.
-- - For on-the-go interviews, employers can change details as needed.

SELECT ApplicationID, JobTitle, CompanyName, ApplicationStatus
FROM JobApplication
JOIN Job ON JobApplication.JobID = Job.JobID
JOIN Company ON Job.CompanyID = Company.CompanyID
WHERE ApplicantID = '1';

SELECT InterviewTypeID FROM Interview WHERE ApplicationID = '631';

DELETE FROM Interview WHERE ApplicationID = '1';

SELECT InterviewTimeFrom, InterviewTimeTo
FROM InPersonInterviewDate
WHERE ApplicationID = '1' AND Booked != '0';

SELECT InterviewTimeFrom, InterviewTimeTo, InterviewLink
FROM OnTheGoInterview
WHERE ApplicationID = '2';

INSERT INTO OnTheGoInterview (ApplicationID, InterviewTimeFrom, InterviewTimeTo, InterviewLink)
VALUES ("APPLICATION_ID", "FROM", "TO", "LINK");

SELECT InPersonInterview.ApplicationID, InPersonInterview.InterviewTypeID, Applicant.FirstName, Applicant.LastName, InterviewTimeFrom, InterviewTimeTo FROM InPersonInterview
JOIN InPersonInterviewDate ON InPersonInterview.ApplicationID = InPersonInterviewDate.ApplicationID
JOIN JobApplication ON InPersonInterview.ApplicationID = JobApplication.ApplicationID
JOIN Job ON JobApplication.JobID = Job.JobID
JOIN Applicant ON JobApplication.ApplicantID = Applicant.ApplicantID
WHERE Job.CompanyID = '327';

 AND Booked != '0';
 
SELECT OnTheGoInterview.ApplicationID, OnTheGoInterview.InterviewTypeID, Applicant.FirstName, Applicant.LastName, InterviewTimeFrom, InterviewTimeTo, InterviewLink FROM OnTheGoInterview
JOIN JobApplication ON OnTheGoInterview.ApplicationID = JobApplication.ApplicationID
JOIN Job ON JobApplication.JobID = Job.JobID
JOIN Applicant ON JobApplication.ApplicantID = Applicant.ApplicantID
WHERE Job.CompanyID = '272';

INSERT INTO InPersonInterviewDate (ApplicationID, InterviewTimeFrom, InterviewTimeTo)
VALUES ("APPLICATION_ID", "FROM", "TO");

UPDATE InPersonInterviewDate
SET Booked = '1'
WHERE ApplicationID = 'APPLICATION_ID';

-- For each job marked as "interviewing", run the following
SELECT * FROM Interview
LEFT JOIN InPersonInterview ON Interview.ApplicationID = InPersonInterview.ApplicationID
LEFT JOIN OnTheGoInterview ON Interview.ApplicationID = OnTheGoInterview.ApplicationID
WHERE Interview.ApplicationID = "1";

SELECT * FROM JobApplication
JOIN Job ON JobApplication.JobID = Job.JobID
JOIN Applicant ON JobApplication.ApplicantID = Applicant.ApplicantID
WHERE CompanyID = "272";

SELECT * FROM Applicant
WHERE ApplicantID = "37";

SELECT * FROM CourseApplicant
JOIN Course ON CourseApplicant.CourseID = Course.CourseID
WHERE ApplicantID = "37";

SELECT * FROM WorkExperience
WHERE ApplicantID = "37";

SELECT * FROM ExtracurricularExperience
WHERE ApplicantID = "37";

SELECT * FROM Skill
WHERE ApplicantID = "37";

SELECT CompanyID = "272"
FROM JobApplication
JOIN Job ON JobApplication.JobID = Job.JobID
WHERE ApplicationID = "266";

INSERT IGNORE INTO greeliving.Applicant(ApplicantID,AuthenticationID,FirstName,LastName,Birthdate,Gender,Email,Phone,Nationality,CountryOfResidence,City,District,StreetAddress,JobTitle,ExperienceLevel,EducationBackground,CareerGoal) VALUES (36,'','Pincas','Yablsley','2009-03-31','Male','pyablsleyz@tinyurl.com',5996957061,NULL,'Solomon Islands','Rila',NULL,NULL,'Teacher',NULL,'High school degree','Nulla ut erat id mauris vulputate elementum. Nullam varius. Nulla facilisi.');

SELECT * FROM Job
JOIN Specialization ON Job.SpecializationID = Specialization.SpecializationID
JOIN Company ON Job.CompanyID = Company.CompanyID
WHERE (JobTitle LIKE '%log%' OR CompanyName LIKE '%log%') AND WorkingFormat = 'Remote' AND ExperienceRequirement = 'Internship';

SELECT * FROM Job JOIN Specialization ON Job.SpecializationID = Specialization.SpecializationID JOIN Company ON Job.CompanyID = Company.CompanyID WHERE CompanySize = '51-100 employees';

UPDATE greeliving.Job
SET ApplicationDeadline = "2024-12-31 23:59:59"
WHERE JobID >= '1';

DELETE FROM greeliving.JobApplication WHERE ApplicationStatus = "Saved";