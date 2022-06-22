import React from 'react'
import { FormControl, Table } from 'react-bootstrap'
import ReferenceItem from '../../components/ReferenceItem'
import useTextFilter from '../../hooks/useTextFilter'
import ButtonWithLoading from '../../components/ButtonWithLoading'
import useSuccessIcon from '../../hooks/useSuccessIcon'
import SuccessIcon from '../../components/SuccessIcon'

const TypedReferences = ({
    isLoading,
    isFetching,
    isSaving,
    references,
    updateReference,
    saveReferences,
    showHide = true,
}) => {
    const { textSearch, handleTextSearchChange, containsTextSearch } = useTextFilter()
    const { isShowSuccessIcon, showSuccessIcon } = useSuccessIcon()
    const selectedCount = references.filter(({ selected, hidden }) => selected || hidden).length

    const applyFilter = (item) => {
        return containsTextSearch([
            item.resource,
            item.name,
            item.initialResource,
            item.route,
            item.routePath,
        ].join(''))
    }

    const handleSaveBtnClick = () => {
        saveReferences().then(() => {
            showSuccessIcon()
        })
    }

    const saveBtn = (
        <div>
            <ButtonWithLoading
                disabled={selectedCount === 0 || isLoading || isSaving}
                active={selectedCount > 0}
                isLoading={isSaving}
                label="Apply Changes"
                className="mb-2"
                onClick={handleSaveBtnClick}
            />
            <SuccessIcon isShow={isShowSuccessIcon}/>
        </div>
    )

    return (
        <div>

            {references.length > 10 && saveBtn}

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

            {saveBtn}

        </div>
    )
}

export default TypedReferences
