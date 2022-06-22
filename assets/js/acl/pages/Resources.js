import React, { useState } from 'react'
import ResourceItem from '../components/ResourceItem'
import { Button, FormControl } from 'react-bootstrap'
import ButtonWithLoading from '../components/ButtonWithLoading'
import useCleaner from '../hooks/useCleaner'
import useResources from '../hooks/useResources'
import SuccessIcon from '../components/SuccessIcon'
import useSuccessIcon from '../hooks/useSuccessIcon'
import { matchOrX } from '../utils/match'

const containsText = (item, text) => {
    const search = text.toLowerCase()
    const searchIn = [
        item.name,
        item.initialName,
        item.description,
        item.initialDescription,
    ].join('').toLowerCase()

    return matchOrX(searchIn, search)
}

const Resources = () => {
    const {
        isLoading,
        isSaving,
        resources,
        selectedCount,
        updateResource,
        addResource,
        saveResources
    } = useResources()
    const { isCleaning, cleanResources } = useCleaner()
    const [textSearch, setTextSearch] = useState('')
    const { isShowSuccessIcon, showSuccessIcon } = useSuccessIcon()

    const handleCleanClick = () => {
        cleanResources()
    }

    const handleTextSearchChange = (event) => {
        setTextSearch(event.target.value)
    }

    const handleSaveBtnClick = (event) => {
        saveResources().then(() => {
            showSuccessIcon()
        })
    }

    const filterByText = (item) => {
        if (textSearch.length < 3) {
            return true
        }

        return containsText(item, textSearch)
    }

    if (isLoading) {
        return <h3>Loading...</h3>
    }

    const saveBtn = (
        <div>
            <ButtonWithLoading
                disabled={selectedCount === 0 || isSaving || isLoading}
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
            <div className="d-flex gap-2 align-items-center justify-content-between border-bottom pb-3">
                <h3 className="m-0">Resources Configuration</h3>
                <Button
                    variant="outline-secondary"
                    className="ms-auto"
                    onClick={addResource}
                >Create Resource</Button>
                <ButtonWithLoading
                    isLoading={isCleaning}
                    variant="outline-danger"
                    label="Clean Unused Resources"
                    loadingLabel="Cleaning Resources..."
                    onClick={handleCleanClick}
                    minWidth={165}
                />
            </div>

            <section className="mt-5">

                {saveBtn}

                <FormControl
                    className="mb-2"
                    placeholder="Search..."
                    value={textSearch}
                    onChange={handleTextSearchChange}
                />

                <table className="table table-bordered">
                    <thead>
                    <tr>
                        <th className="text-center align-middle">Apply</th>
                        <th className="text-center align-middle">Resource</th>
                        <th className="text-center align-middle">Description</th>
                        <th className="text-center align-middle">Created By</th>
                        <th className="text-center align-middle">References</th>
                    </tr>
                    </thead>
                    <tbody>
                    {resources.filter(filterByText).map(item => (
                        <ResourceItem
                            key={item.key}
                            item={item}
                            onEdit={updateResource}
                        />
                    ))}
                    </tbody>
                </table>

                {saveBtn}

            </section>
        </div>
    )
}

export default Resources
