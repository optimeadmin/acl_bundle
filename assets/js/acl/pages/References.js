import React from 'react'
import { Nav, Tab } from 'react-bootstrap'
import LoadingIcon from '../components/LoadingIcon'
import { useReferencesQuery } from '../hooks/references'
import ReferencesByType from './section/ReferencesByType'

export default function References () {
  const existentReferences = useReferencesQuery('existent')
  const newsReferences = useReferencesQuery('news')
  const hiddenReferences = useReferencesQuery('hidden')
  const { isFetching, isLoading } = existentReferences

  const defaultTab = existentReferences.serverCount > 0 ? 'persisted' : 'news'

  if (isLoading) {
    return <h3>Loading...</h3>
  }

  return (
    <div className={`acl-page-container ${isFetching ? 'is-loading' : ''}`}>
      <div className="d-flex gap-2 align-items-center border-bottom pb-3">
        <h3 className="m-0">Controllers Configuration</h3>
        <LoadingIcon isLoading={isFetching} size="md" className="ms-2" />
      </div>

      <section className="mt-4">

        <Tab.Container defaultActiveKey={defaultTab} transition={false}>
          <Nav variant="pills">
            {existentReferences.length > 0 && (
              <Nav.Item>
                <Nav.Link role="button" eventKey="persisted">Persisted</Nav.Link>
              </Nav.Item>
            )}
            {newsReferences.length > 0 && (
              <Nav.Item>
                <Nav.Link role="button" eventKey="news">News</Nav.Link>
              </Nav.Item>
            )}
            {hiddenReferences.length > 0 && (
              <Nav.Item>
                <Nav.Link role="button" eventKey="hidden">Hidden</Nav.Link>
              </Nav.Item>
            )}
          </Nav>

          <hr />

          <Tab.Content className="mt-5">
            {existentReferences.length > 0 && (
              <Tab.Pane eventKey="persisted" unmountOnExit={true}>
                <ReferencesByType
                  title="Persisted"
                  isLoading={existentReferences.isLoading}
                  references={existentReferences.references}
                />
              </Tab.Pane>
            )}
            {newsReferences.length > 0 && (
              <Tab.Pane eventKey="news" unmountOnExit={true}>
                <ReferencesByType
                  title="News"
                  isLoading={newsReferences.isLoading}
                  references={newsReferences.references}
                />
              </Tab.Pane>
            )}
            {hiddenReferences.length > 0 && (
              <Tab.Pane eventKey="hidden" unmountOnExit={true}>
                <ReferencesByType
                  title="Hidden"
                  isLoading={hiddenReferences.isLoading}
                  references={hiddenReferences.references}
                  showHide={false}
                />
              </Tab.Pane>
            )}
          </Tab.Content>
        </Tab.Container>

      </section>
    </div>
  )
}
