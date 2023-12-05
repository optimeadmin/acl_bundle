import React from 'react'
import { Button, FormControl } from 'react-bootstrap'
import ButtonWithLoading from '../components/ButtonWithLoading'
import LoadingIcon from '../components/LoadingIcon'
import ResourceItem from '../components/ResourceItem'
import SuccessIcon from '../components/SuccessIcon'
import { useManageResources, useSaveResources } from '../hooks/resources'
import useCleaner from '../hooks/useCleaner'
import useSuccessIcon from '../hooks/useSuccessIcon'
import useTextFilter from '../hooks/useTextFilter'

const getItemContents = item => {
  return [item.name, item.initialName, item.description, item.initialDescription].join('')
}

export default function Resources () {
  const { addResource, updateResource, resources, selectedCount, isLoading } = useManageResources()
  const { isSaving, saveResources } = useSaveResources()
  const { isCleaning, cleanResources } = useCleaner()
  const { textSearch, handleTextSearchChange, filterByText } = useTextFilter()
  const { isShowSuccessIcon, showSuccessIcon } = useSuccessIcon()

  const filteredResources = filterByText(resources, getItemContents)

  async function handleSaveBtnClick () {
    await saveResources()
    showSuccessIcon()
  }

  if (isLoading) {
    return <h3>Loading...</h3>
  }

  const saveBtn = (
    <div>
      <ButtonWithLoading
        disabled={selectedCount === 0 || isSaving || isLoading}
        isLoading={isSaving}
        label='Apply Changes'
        className='mb-2'
        onClick={handleSaveBtnClick}
      />
      <SuccessIcon isShow={isShowSuccessIcon} />
    </div>
  )

  return (
    <div className={`acl-page-container ${isLoading ? 'is-loading' : ''}`}>
      <div className='d-flex gap-2 align-items-center justify-content-between border-bottom pb-3'>
        <h3 className='m-0'>Resources Configuration</h3>
        <LoadingIcon isLoading={isLoading} size='md' className='ms-2' />
        <Button variant='outline-secondary' className='ms-auto' onClick={addResource}>
          Create Resource
        </Button>
        <ButtonWithLoading
          isLoading={isCleaning}
          variant='outline-danger'
          label='Clean Unused Resources'
          loadingLabel='Cleaning Resources...'
          onClick={cleanResources}
          minWidth={165}
        />
      </div>

      <section className='mt-5'>
        {saveBtn}

        <FormControl className='mb-2' placeholder='Search...' value={textSearch} onChange={handleTextSearchChange} />

        <table className='table table-bordered'>
          <thead>
            <tr>
              <th className='text-center align-middle'>Apply</th>
              <th className='text-center align-middle'>Resource</th>
              <th className='text-center align-middle'>Description</th>
              <th className='text-center align-middle'>Created By</th>
              <th className='text-center align-middle'>References</th>
            </tr>
          </thead>
          <tbody>
            {filteredResources.map(item => <ResourceItem key={item.key} item={item} onEdit={updateResource} />)}
            {filteredResources.length === 0 &&
              <tr>
                <td className='text-center' colSpan={100}>
                  No items found
                </td>
              </tr>}
          </tbody>
        </table>

        {saveBtn}
      </section>
    </div>
  )
}
