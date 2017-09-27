import React from 'react'
import { IndexLink, Link } from 'react-router'
import PropTypes from 'prop-types'

export const PageLayout = ({header, content, children, footer}) => (
  <div className='child'>
    {header}
    {content || children}
    {footer}
  </div>
)

PageLayout.propTypes = {
  header: PropTypes.object,
  children: PropTypes.node,
  content: PropTypes.object,
  footer: PropTypes.object
}

export default PageLayout
