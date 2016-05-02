/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import java.util.Collection;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlTransient;
import org.codehaus.jackson.annotate.JsonIgnore;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "anno_type")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "AnnoType.findAll", query = "SELECT a FROM AnnoType a"),
    @NamedQuery(name = "AnnoType.findByAnnoTypeId", query = "SELECT a FROM AnnoType a WHERE a.annoTypeId = :annoTypeId"),
    @NamedQuery(name = "AnnoType.findByAnnoTypeName", query = "SELECT a FROM AnnoType a WHERE a.annoTypeName = :annoTypeName")})
public class AnnoType implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    //@NotNull
    @Column(name = "anno_type_id")
    private Integer annoTypeId;
    @Basic(optional = false)
    //@NotNull
    @Size(min = 1, max = 20)
    @Column(name = "anno_type_name")
    private String annoTypeName;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "annoTypeId")
    private Collection<Annotation> annotationCollection;

    public AnnoType() {
    }

    public AnnoType(Integer annoTypeId) {
        this.annoTypeId = annoTypeId;
    }

    public AnnoType(Integer annoTypeId, String annoTypeName) {
        this.annoTypeId = annoTypeId;
        this.annoTypeName = annoTypeName;
    }

    public Integer getAnnoTypeId() {
        return annoTypeId;
    }

    public void setAnnoTypeId(Integer annoTypeId) {
        this.annoTypeId = annoTypeId;
    }

    public String getAnnoTypeName() {
        return annoTypeName;
    }

    public void setAnnoTypeName(String annoTypeName) {
        this.annoTypeName = annoTypeName;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<Annotation> getAnnotationCollection() {
        return annotationCollection;
    }

    public void setAnnotationCollection(Collection<Annotation> annotationCollection) {
        this.annotationCollection = annotationCollection;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (annoTypeId != null ? annoTypeId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof AnnoType)) {
            return false;
        }
        AnnoType other = (AnnoType) object;
        if ((this.annoTypeId == null && other.annoTypeId != null) || (this.annoTypeId != null && !this.annoTypeId.equals(other.annoTypeId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.AnnoType[ annoTypeId=" + annoTypeId + " ]";
    }
    
}
