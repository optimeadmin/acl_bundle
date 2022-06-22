import React from 'react'
import { FormControl, Table } from 'react-bootstrap'
import ReferenceItem from '../../components/ReferenceItem'
import useTextFilter from '../../hooks/useTextFilter'

const TypedReferences = ({
    isLoading,
    isFetching,
    references,
    updateReference,
    showHide = true,
}) => {
    const { textSearch, handleTextSearchChange, containsTextSearch } = useTextFilter()

    const applyFilter = (item) => {
        return containsTextSearch([
            item.resource,
            item.name,
            item.initialResource,
            item.route,
            item.routePath,
        ].join(''))
    }

    return (
        <div>
            <FormControl
                className="mb-2"
                placeholder="Search..."
                value={textSearch}
                onChange={handleTextSearchChange}
            />

            <div className="table-responsive">
                <Table bordered size="sm">
                    <thead>
                    <tr>
                        <th>Apply</th>
                        <th style={{ minWidth: 200 }}>Resource</th>
                        <th>Reference</th>
                        <th>Route Name</th>
                        <th>Route Path</th>
                        {showHide && (<th>Hide</th>)}
                    </tr>
                    </thead>
                    <tbody>
                    {references.filter(applyFilter).map(item => (
                        <ReferenceItem
                            key={item.identifier}
                            item={item}
                            onEdit={updateReference}
                            showHide={showHide}
                        />
                    ))}
                    </tbody>
                </Table>
            </div>
        </div>
    )
}

export default TypedReferences
