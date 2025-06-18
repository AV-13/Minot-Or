import React from 'react';
import Button from '../atoms/Button/Button';
import Select from '../atoms/Select';
import { ROLES } from '../../constants/roles';

export default function UserRow({ user, onDelete, onRoleChange }) {
    return (
        <tr>
            <td>{user.email}</td>
            <td>{user.name}</td>
            <td>{Object.values(user.role)}</td>
            <td>
                <Select
                    options={ROLES}
                    value={Object.values(user.role)[0].replace(/^ROLE_/, '')}
                    onChange={e => onRoleChange(user.id, e.target.value)}
                />
            </td>
            <td>
                <Button onClick={() => onDelete(user.id)}>Supprimer</Button>
            </td>
        </tr>
    );
}