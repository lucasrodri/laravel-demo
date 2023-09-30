import React from "react";
import "../styles.css";
import { useState } from "react";
import { useEffect } from "react";

export const ContactPage = () => {
  const [contacts, setContacts] = useState([]);

  useEffect(() => {
    fetch("https://randomuser.me/api/?results=3")
      .then(response => response.json())
      .then(data => {
        setContacts(data.results);
      });
  }, []);

  return (
    <>
      {contacts.map(contact => (
        <ContactCard
          avatar={contact.picture.large}
          name={contact.name.first + " " + contact.name.last}
          email={contact.email}
          age={contact.dob.age}
        />
      ))}
    </>
  );
};

const ContactCard = props => {
  const [showAge, setShowAge] = useState(false);

  return (
    <div className="contact-card">
      <img src={props.avatar} alt="profile" />
      <div className="user-details">
        <p>Name: {props.name}</p>
        <p>Email: {props.email}</p>
        {props.age ? (
          <div>
            <button onClick={() => setShowAge(!showAge)}>Toggle Age</button>
            {showAge && <p>Age: {props.age}</p>}
          </div>
        ) : null}
      </div>
    </div>
  );
};
