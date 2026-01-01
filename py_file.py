import streamlit as st
from random import randint
'''
insert into exam_applications(`application_id`,`full_name`,`date_of_birth`,`age`,`gender`,`email`,`phone`,`father_name`,`aadhar`,`caste`,`address`,`ssc_year`,`ssc_percentage`,`inter_year`,`inter_percentage`,`degree_year`,`degree_percentage`,`position`,`exam_center`,`transaction_id`)
 values("MAFO-20251224-2848","PARVATHAM AVINASH","1999-12-18",26,"Male" ,"parvathamavinash8190@gmail.com","6304927262","PARVATHAM SRINIVAS" ,"576700517387","BC-B","1-7-116/A Nallalabavi Road, Suryapet, Suryapet dist, Telangana.",2015,"9.3",2017,"9.53",0 ,"","MAFO","Nalgonda","535611981923");

'''
if "query" not in st.session_state:
    st.session_state.query=""
with st.sidebar:
    st.text_area('Text', height=350)
    st.text_area("Output",value=st.session_state.query, height=350)

c1,c2=st.columns(2)
with c1:
    application_id=st.text_input('application_id')
    full_name=st.text_input('full_name')
    date_of_birth=st.date_input('date_of_birth', format="YYYY-MM-DD")
    # age=st.number_input('age')
    gender=st.selectbox("Gender", ("Male", "Female"))
    email=st.text_input('email')
    phone=st.text_input('phone')
    father_name=st.text_input('father_name')
    aadhar=st.text_input('aadhar')
    caste=st.text_input('caste')
with c2:
    address=st.text_input('address')
    ssc_year=st.text_input('ssc_year')
    ssc_percentage=st.text_input('ssc_percentage')
    inter_year=st.text_input('inter_year')
    inter_percentage=st.text_input('inter_percentage')
    degree_year=st.text_input('degree_year')
    degree_percentage=st.text_input('degree_percentage')
    position=st.selectbox('position', ("MAFO", "RAFO", "ZAFO", "DAFO"))
    exam_center=st.selectbox('exam_center', ("Adilabad","Alluri Sitharama Raju","Anakapalli","Anantapur","Ananthapuramu","Annamayya","Bapatla ","Chittoor","Dr. B. R. Ambedkar Konaseema","East Godavari","Eluru","Guntur","Hyderabad","Kakinada","Karimnagar","Khammam","Krishna","Kurnool","Madanapalle","Mahabubnagar","Markapuram","Medak","Nalgonda","Nandyal","Nizamabad","NTR","Palnadu","Parvathipuram Manyam","Prakasam","Ranga Reddy","Sri Potti Sriramulu Nellore","Sri Sathya Sai","Srikakulam","Tirupati","Visakhapatnam","Vizianagaram","Warangal","West Godavari","YSR Kadapa"))
transaction_id=st.text_input('transaction_id')

def get_query():
    q=f'''insert into exam_applications(`application_id`,`full_name`,`date_of_birth`,`age`,`gender`,`email`,`phone`,`father_name`,`aadhar`,`caste`,`address`,`ssc_year`,`ssc_percentage`,`inter_year`,`inter_percentage`,`degree_year`,`degree_percentage`,`position`,`exam_center`,`transaction_id`) values("{position}-20251228-{randint(1000,9999)}","{full_name}","{date_of_birth}",{2025-date_of_birth.year},"{gender}" ,"{email}","{phone}","{father_name}" ,"{aadhar}","{caste}","{address}",{ssc_year},"{ssc_percentage}",{inter_year},"{inter_percentage}",{degree_year} ,"{degree_percentage}","{position}","{exam_center}","{transaction_id}");'''
    st.session_state.query=q

btn=st.button('Get query', on_click=get_query)