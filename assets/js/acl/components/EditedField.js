import React from 'react';

const EditedField = ({children, edited = false}) => {

    return (
        <div className="d-inline-block">
            {children}
            <div className={
                `animate__animated border-bottom border-2 border-secondary ${
                    edited ? 'animate__bounceIn' : 'animate__fadeOut'
                }`
            }></div>
        </div>
    );
};

export default EditedField;
