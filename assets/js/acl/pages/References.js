import React from 'react'
import { Nav, Tab } from 'react-bootstrap'
import TypedReferences from './section/TypedReferences'
import useReferences from '../hooks/useReferences'

const References = () => {
    const existentReferences = useReferences('existent')
    const newsReferences = useReferences('news')
    const hiddenReferences = useReferences('hidden')

    return (
        <div>
            <div className="d-flex gap-2 align-items-center justify-content-between border-bottom pb-3">
                <h3 className="m-0">Controllers Configuration</h3>
            </div>

            <section className="mt-5">

                <Tab.Container defaultActiveKey="persisted">
                    <Nav variant="pills">
                        {existentReferences.count > 0 && (
                            <Nav.Item>
                                <Nav.Link role="button" eventKey="persisted">Persisted</Nav.Link>
                            </Nav.Item>
                        )}
                        {newsReferences.count > 0 && (
                            <Nav.Item>
                                <Nav.Link role="button" eventKey="news">News</Nav.Link>
                            </Nav.Item>
                        )}
                        {hiddenReferences.count > 0 && (
                            <Nav.Item>
                                <Nav.Link role="button" eventKey="hidden">Hidden</Nav.Link>
                            </Nav.Item>
                        )}
                    </Nav>

                    <hr/>

                    <Tab.Content className="mt-5">
                        {existentReferences.count > 0 && (
                            <Tab.Pane eventKey="persisted">
                                <TypedReferences
                                    title="Persisted"
                                    {...existentReferences}
                                />
                            </Tab.Pane>
                        )}
                        {newsReferences.count > 0 && (
                            <Tab.Pane eventKey="news">
                                <TypedReferences
                                    title="News"
                                    {...newsReferences}
                                />
                            </Tab.Pane>
                        )}
                        {hiddenReferences.count > 0 && (
                            <Tab.Pane eventKey="hidden">
                                <TypedReferences
                                    title="Hidden"
                                    {...hiddenReferences}
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

export default References
