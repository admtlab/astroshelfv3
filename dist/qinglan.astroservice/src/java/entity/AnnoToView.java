/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "anno_to_view")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "AnnoToView.findAll", query = "SELECT a FROM AnnoToView a"),
    @NamedQuery(name = "AnnoToView.findByAnnoToViewId", query = "SELECT a FROM AnnoToView a WHERE a.annoToViewId = :annoToViewId"),
    @NamedQuery(name = "AnnoToView.findByViewName", query = "SELECT a FROM AnnoToView a WHERE a.viewName = :viewName")})
public class AnnoToView implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "anno_to_view_id")
    private Long annoToViewId;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 45)
    @Column(name = "view_name")
    private String viewName;
    @Basic(optional = false)
    @NotNull
    @Lob
    @Size(min = 1, max = 65535)
    @Column(name = "query")
    private String query;
    @JoinColumn(name = "anno_src_id", referencedColumnName = "anno_id")
    @OneToOne(optional = false)
    private Annotation annoSrcId;

    public AnnoToView() {
    }

    public AnnoToView(Long annoToViewId) {
        this.annoToViewId = annoToViewId;
    }

    public AnnoToView(Long annoToViewId, String viewName, String query) {
        this.annoToViewId = annoToViewId;
        this.viewName = viewName;
        this.query = query;
    }

    public Long getAnnoToViewId() {
        return annoToViewId;
    }

    public void setAnnoToViewId(Long annoToViewId) {
        this.annoToViewId = annoToViewId;
    }

    public String getViewName() {
        return viewName;
    }

    public void setViewName(String viewName) {
        this.viewName = viewName;
    }

    public String getQuery() {
        return query;
    }

    public void setQuery(String query) {
        this.query = query;
    }

    public Annotation getAnnoSrcId() {
        return annoSrcId;
    }

    public void setAnnoSrcId(Annotation annoSrcId) {
        this.annoSrcId = annoSrcId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (annoToViewId != null ? annoToViewId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof AnnoToView)) {
            return false;
        }
        AnnoToView other = (AnnoToView) object;
        if ((this.annoToViewId == null && other.annoToViewId != null) || (this.annoToViewId != null && !this.annoToViewId.equals(other.annoToViewId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.AnnoToView[ annoToViewId=" + annoToViewId + " ]";
    }
    
}
