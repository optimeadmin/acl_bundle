import React from 'react';

const ResourcesRoles = () => {
    return (
        <div>
            <h3 className="border-bottom pb-3">Access Control Configuration</h3>

            <section>

                <table className="table table-bordered">
                    <thead>
                    <tr>
                        <th className="text-center align-middle" rowSpan="2">Resource</th>
                        <th className="text-center align-middle" colSpan="200">Roles</th>
                    </tr>
                    <tr>
                        <th className="text-center align-middle">All</th>
                        <th className="align-middle text-nowrap">
                            <div className="d-flex align-items-center">
                                <span>ROLE_USER</span>
                            </div>
                        </th>
                        <th className="align-middle text-nowrap">
                            <div className="d-flex align-items-center">
                                <span>ROLE_ADMIN</span>
                            </div>
                        </th>
                        <th className="align-middle text-nowrap">
                            <div className="d-flex align-items-center">
                                <span>ROLE_OPTIME</span>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr className="acl-resource-container" data-resource="edit">
                        <td className="fw-bold">&nbsp;edit</td>
                        <td className="text-center align-middle js-acl-select-all-roles">
                            <div><input type="checkbox" id="access_control_form_93_all"
                                        name="access_control_form[93][all]" required="required" value="1"
                                        className="js-select-all-roles-checkbox"/></div>
                        </td>
                        <td className="text-center align-middle">
                            <div><input type="checkbox" id="access_control_form_93_roles_0"
                                        name="access_control_form[93][roles][]" value="ROLE_USER" checked="checked"/>
                            </div>
                        </td>
                        <td className="text-center align-middle">
                            <div><input type="checkbox" id="access_control_form_93_roles_1"
                                        name="access_control_form[93][roles][]" value="ROLE_ADMIN" checked="checked"/>
                            </div>
                        </td>
                        <td className="text-center align-middle">
                            <div><input type="checkbox" id="access_control_form_93_roles_2"
                                        name="access_control_form[93][roles][]" value="ROLE_OPTIME"/></div>
                        </td>
                    </tr>
                    </tbody>

                </table>

            </section>
        </div>
    );
};

export default ResourcesRoles;
