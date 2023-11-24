-- View
SELECT CompanyName, CompanySize, Phone, Email, Introduction 
FROM Company WHERE CompanyID = ?;

-- Edit
UPDATE Company
SET CompanyName = ?, CompanySize = ?, Phone = ?, Introduction = ?
WHERE CompanyID = ?;

-- View
SELECT FirstName, LastName, Birthdate, Gender, Email, Phone, 
       Nationality, CountryOfResidence, City, District, 
       StreetAddress, JobTitle, ExperienceLevel, 
       EducationBackground, CareerGoal 
FROM Applicant
WHERE ApplicantID = ?;

-- Edit
UPDATE Applicant 
SET FirstName = ?, LastName = ?, Birthdate = ?, Gender = ?, 
    Phone = ?, Nationality = ?, CountryOfResidence = ?, 
    City = ?, District = ?, StreetAddress = ?, JobTitle = ?, 
    ExperienceLevel = ?, EducationBackground = ?, CareerGoal = ?
WHERE ApplicantID = ?;

-- Create an applicant in the DB
INSERT INTO Applicant (AuthenticationID, FirstName, LastName, 
                      Birthdate, Gender, Email, Phone, Nationality, 
                      CountryOfResidence, City, District, 
                      StreetAddress, JobTitle, ExperienceLevel, 
                      EducationBackground, CareerGoal)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);

-- Create an employer in the DB
INSERT INTO Company (AuthenticationID, CompanyName, CompanySize, 
                    Phone, Email, Introduction) 
VALUES (?,?,?,?,?,?);

INSERT INTO JobApplication (JobID, ApplicantID, CV, 
                           StatementOfPurpose, ExpectToGain, 
                           Questions, ApplicationStatus)
VALUES (?, ?, ?, ?, ?, ?, 'Applying');

SELECT JobID, JobTitle, DatePosted, ApplicationDeadline, Salary, 
       WorkingLocation, WorkingFormat, ExperienceRequirement, 
       SpecializationName, CompanyName, CompanySize 
FROM Job 
JOIN Specialization 
ON Job.SpecializationID = Specialization.SpecializationID 
JOIN Company ON Job.CompanyID = Company.CompanyID
ORDER BY DatePosted DESC;

-- Additional WHERE conditions can be added depending on the user's query.
WHERE (JobTitle LIKE %?% OR CompanyName LIKE %?%);
WHERE Salary >= ?;
WHERE ExperienceRequirement = ?;
WHERE WorkingFormat = ?;
WHERE CompanySize = ?;
WHERE Job.SpecializationID = ?;

SELECT JobTitle, ApplicationDeadline, Salary, WorkingLocation, 
       SpecializationName, ExperienceRequirement, WorkingFormat, 
       ScopeOfWork, Benefits, CompanyName, Email, Phone, 
       Introduction 
FROM Job 
JOIN Specialization 
ON Job.SpecializationID = Specialization.SpecializationID 
JOIN Company ON Job.CompanyID = Company.CompanyID WHERE JobID = ?;

INSERT INTO Job (CompanyID, JobTitle, ApplicationDeadline, Salary, 
                WorkingLocation, SpecializationID, 
                ExperienceRequirement, WorkingFormat, ScopeOfWork, 
                Benefits) 
VALUES (?,?,?,?,?,?,?,?,?,?);

-- First, unbook every date for this particular application.
UPDATE InPersonInterviewDate 
SET Booked = '0' 
WHERE ApplicationID = ?;

-- Then, book the specified date.
UPDATE InPersonInterviewDate 
SET Booked = '1' 
WHERE ApplicationID = ? AND InterviewTimeFrom = ? 
      AND InterviewTimeTo = ?;

SELECT ApplicationID, FirstName, LastName, Job.JobTitle, 
       TimeOfApplication, ApplicationStatus FROM JobApplication 
JOIN Applicant 
ON JobApplication.ApplicantID = Applicant.ApplicantID 
JOIN Job ON JobApplication.JobID = Job.JobID 
WHERE CompanyID = ? 
ORDER BY TimeOfApplication DESC;

-- If an online interview has previously been set for this 
application, remove it.
DELETE FROM Interview 
WHERE ApplicationID = ? AND InterviewTypeID = '2';

-- Create an in-person interview.
INSERT IGNORE INTO Interview VALUES (?, '1');
INSERT IGNORE INTO InPersonInterview (ApplicationID) VALUES (?);

-- If an in-person interview has previously been set for this application, get all the dates.
SELECT InterviewTimeFrom, InterviewTimeTo 
FROM InPersonInterviewDate 
WHERE ApplicationID = ?;

-- Remove some dates if necessary.
DELETE FROM InPersonInterviewDate 
WHERE ApplicationID = ? AND InterviewTimeFrom = ? 
      AND InterviewTimeTo = ?;

-- Add the new in-person dates.
INSERT IGNORE INTO InPersonInterviewDate 
  (ApplicationID, InterviewTimeFrom, InterviewTimeTo) 
VALUES (?, ?, ?);

-- If an in-person interview has previously been set for this application, remove it.
DELETE FROM Interview 
WHERE ApplicationID = ? AND InterviewTypeID = '1';

-- Create an online interview.
INSERT IGNORE INTO Interview VALUES (?, '2');

INSERT INTO OnTheGoInterview 
  (ApplicationID, InterviewTimeFrom, InterviewTimeTo, InterviewLink) 
VALUES (?,?,?,?)
ON DUPLICATE KEY UPDATE 
InterviewTimeFrom = ?, InterviewTimeTo = ?, InterviewLink = ?;

UPDATE JobApplication 
SET ApplicationStatus = ? 
WHERE ApplicationID = ?;

-- Get basic profile
SELECT FirstName, LastName, Birthdate, Gender, Email, Phone, 
       Nationality, CountryOfResidence, City, District, 
       StreetAddress, JobTitle, ExperienceLevel, 
       EducationBackground, CareerGoal 
FROM Applicant 
WHERE ApplicantID = ?;

-- Get courses they have enrolled in
SELECT CourseName, CourseStatus FROM CourseApplicant
JOIN Course ON CourseApplicant.CourseID = Course.CourseID
WHERE ApplicantID = ?;

-- Get the list of applications
SELECT ApplicationID, JobApplication.JobID, TimeOfApplication, 
       ApplicationStatus, CompanyName, JobTitle, WorkingLocation, 
       WorkingFormat 
FROM JobApplication 
JOIN Job ON JobApplication.JobID = Job.JobID 
JOIN Company ON Job.CompanyID = Company.CompanyID
WHERE ApplicantID = ? 
ORDER BY TimeOfApplication DESC;

-- For applications marked as "Interviewing", determine the interview format.
SELECT InterviewTypeID FROM Interview WHERE ApplicationID = ?;

-- If the interview type is in-person (id 1), show possible dates
SELECT InterviewTimeFrom, InterviewTimeTo, Booked 
FROM InPersonInterviewDate 
WHERE ApplicationID = ?;

-- If the interview type is online (id 2), show the date.
SELECT InterviewTimeFrom, InterviewTimeTo 
FROM OnTheGoInterview 
WHERE ApplicationID = ?;

-- Get all booked in-person interviews.
SELECT InPersonInterview.ApplicationID, 
       InPersonInterview.InterviewTypeID, Applicant.FirstName, 
       Applicant.LastName, InterviewTimeFrom, InterviewTimeTo, 
       Job.JobTitle
FROM InPersonInterview
JOIN InPersonInterviewDate 
ON InPersonInterview.ApplicationID = InPersonInterviewDate.ApplicationID
JOIN JobApplication 
ON InPersonInterview.ApplicationID = JobApplication.ApplicationID
JOIN Job 
ON JobApplication.JobID = Job.JobID
JOIN Applicant 
ON JobApplication.ApplicantID = Applicant.ApplicantID
WHERE Job.CompanyID = ? AND Booked != '0' AND InterviewTimeTo > NOW();

-- Get all online interviews.                           
SELECT OnTheGoInterview.ApplicationID, 
       OnTheGoInterview.InterviewTypeID, Applicant.FirstName, 
       Applicant.LastName, InterviewTimeFrom, InterviewTimeTo, 
       InterviewLink, Job.JobTitle
FROM OnTheGoInterview
JOIN JobApplication 
ON OnTheGoInterview.ApplicationID = JobApplication.ApplicationID
JOIN Job ON JobApplication.JobID = Job.JobID
JOIN Applicant ON JobApplication.ApplicantID = Applicant.ApplicantID
WHERE Job.CompanyID = ? AND InterviewTimeTo > NOW();

-- Save job
INSERT IGNORE INTO SavedJob VALUES (?,?)

-- Unsave job
DELETE FROM SavedJob WHERE JobID = ? AND ApplicantID = ?;

-- View saved jobs
SELECT JobID, JobTitle, CompanyName FROM SavedJob 
JOIN Job ON SavedJob.JobID = Job.JobID
JOIN Company ON Job.CompanyID = Company.CompanyID
WHERE SavedJob.ApplicantID = ?;

INSERT IGNORE INTO CourseApplicant (CourseID, ApplicantID) VALUES (?, ?);