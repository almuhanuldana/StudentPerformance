Table users {
  id int [pk, increment]
  name varchar(100)
  email varchar(150) [unique]
  password varchar(255)
  created_at timestamp
}

Table students {
  id int [pk, increment]
  full_name varchar(150)
  student_number varchar(30) [unique]
  group_name varchar(50)
  email varchar(150)
  created_at timestamp
}

Table subjects {
  id int [pk, increment]
  subject_name varchar(100)
  subject_code varchar(30) [unique]
}

Table grades {
  id int [pk, increment]
  student_id int [not null, ref: > students.id]
  subject_id int [not null, ref: > subjects.id]
  grade decimal(4,2)
  assessment_date date
}
