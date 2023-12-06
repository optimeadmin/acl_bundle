import React from 'react'
import { Button, FormControl } from 'react-bootstrap'
import ButtonWithLoading from '../components/ButtonWithLoading'
import LoadingIcon from '../components/LoadingIcon'
import ResourceItem, { ResourceItemLoading } from '../components/ResourceItem'
import SuccessIcon from '../components/SuccessIcon'
import { useManageResources, useSaveResources } from '../hooks/resources'
import useCleaner from '../hooks/useCleaner'
import useSuccessIcon from '../hooks/useSuccessIcon'
import useTextFilter from '../hooks/useTextFilter'
import { useIsMutating } from '@tanstack/react-query'

const getItemContents = item => {
  return [item.name, item.initialName, item.description, item.initialDescription].join('')
}

export default function Resources () {
  const { addResource, updateResource, resources, selectedCount, isLoading } = useManageResources()
  const { textSearch, handleTextSearchChange, filterByText } = useTextFilter()

  const filteredResources = filterByText(resources, getItemContents)

  const saveBtn = (<SaveButton count={selectedCount} resources={resources} isLoading={isLoading} />)

  return (
    <div className={`acl-page-container ${isLoading ? 'is-loading' : ''}`}>
      <div className='d-flex gap-2 align-items-center justify-content-between border-bottom pb-3'>
        <h3 className='m-0'>Resources Configuration</h3>
        <LoadingIcon isLoading={isLoading} size='md' className='ms-2' />
        <Button variant='outline-secondary' className='ms-auto' onClick={addResource}>
          Create Resource
        </Button>
        <CleanResources />
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
            {isLoading && <ResourceItemLoading />}
            {isLoading && <ResourceItemLoading />}
            {isLoading && <ResourceItemLoading />}
            {!isLoading && filteredResources.map(item =>
              <ResourceItem key={item.key} item={item} onEdit={updateResource} />
            )}
            {!isLoading && filteredResources.length === 0 && (
              <tr>
                <td className='text-center' colSpan={100}>
                  No items found
                </td>
              </tr>
            )}
          </tbody>
        </table>

        {saveBtn}
      </section>
    </div>
  )
}

function CleanResources () {
  const { isCleaning, cleanResources } = useCleaner()

  return (
    <ButtonWithLoading
      isLoading={isCleaning}
      variant='outline-danger'
      label='Clean Unused Resources'
      loadingLabel='Cleaning Resources...'
      onClick={cleanResources}
      minWidth={165}
    />
  )
}

function SaveButton ({ count, resources, isLoading }) {
  const isSaving = useIsMutating({ mutationKey: ['resources'] }) > 0
  const { saveResources } = useSaveResources()
  const { isShowSuccessIcon, showSuccessIcon } = useSuccessIcon()

  async function save () {
    await saveResources(resources)
    showSuccessIcon()
  }

  return (
    <div>
      <ButtonWithLoading
        disabled={count === 0 || isSaving || isLoading}
        isLoading={isSaving}
        label='Apply Changes'
        className='mb-2'
        onClick={save}
      />
      <SuccessIcon isShow={isShowSuccessIcon} />
    </div>
  )
}
