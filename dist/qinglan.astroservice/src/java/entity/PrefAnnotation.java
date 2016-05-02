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
@Table(name = "prefAnnotation")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "PrefAnnotation.findAll", query = "SELECT p FROM PrefAnnotation p"),
    @NamedQuery(name = "PrefAnnotation.findByPrefId", query = "SELECT p FROM PrefAnnotation p WHERE p.prefId = :prefId"),
    @NamedQuery(name = "PrefAnnotation.findByAttributeName", query = "SELECT p FROM PrefAnnotation p WHERE p.attributeName = :attributeName"),
    @NamedQuery(name = "PrefAnnotation.findByOperator", query = "SELECT p FROM PrefAnnotation p WHERE p.operator = :operator"),
    @NamedQuery(name = "PrefAnnotation.findByAttributeValue", query = "SELECT p FROM PrefAnnotation p WHERE p.attributeValue = :attributeValue")})
public class PrefAnnotation implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    //@NotNull
    @Column(name = "pref_id")
    private Long prefId;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 50)
    @Column(name = "attribute_name")
    private String attributeName;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 4)
    @Column(name = "operator")
    private String operator;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 50)
    @Column(name = "attribute_value")
    private String attributeValue;
    @JoinTable(name = "prefAnn_To_PrefQT", joinColumns = {
        @JoinColumn(name = "pref_on_ann_id", referencedColumnName = "pref_id")}, inverseJoinColumns = {
        @JoinColumn(name = "prefqt_id", referencedColumnName = "prefqt_id")})
    @ManyToMany(cascade = CascadeType.PERSIST)//,mappedBy = "prefAnnotationCollection")
    private Collection<PrefQT> prefQTCollection;
    @JoinTable(name = "prefAnn_To_PrefQL", joinColumns = {
        @JoinColumn(name = "pref_on_ann_id", referencedColumnName = "pref_id")}, inverseJoinColumns = {
        @JoinColumn(name = "prefql_id", referencedColumnName = "prefql_id")})
    @ManyToMany(cascade = CascadeType.PERSIST)
    private Collection<PrefQL> prefQLCollection;

    public PrefAnnotation() {
    }

    public PrefAnnotation(Long prefId) {
        this.prefId = prefId;
    }

    public PrefAnnotation(Long prefId, String attributeName, String operator, String attributeValue) {
        this.prefId = prefId;
        this.attributeName = attributeName;
        this.operator = operator;
        this.attributeValue = attributeValue;
    }

    public Long getPrefId() {
        return prefId;
    }

    public void setPrefId(Long prefId) {
        this.prefId = prefId;
    }

    public String getAttributeName() {
        return attributeName;
    }

    public void setAttributeName(String attributeName) {
        this.attributeName = attributeName;
    }

    public String getOperator() {
        return operator;
    }

    public void setOperator(String operator) {
        this.operator = operator;
    }

    public String getAttributeValue() {
        return attributeValue;
    }

    public void setAttributeValue(String attributeValue) {
        this.attributeValue = attributeValue;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<PrefQT> getPrefQTCollection() {
        return prefQTCollection;
    }

    public void setPrefQTCollection(Collection<PrefQT> prefQTCollection) {
        this.prefQTCollection = prefQTCollection;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<PrefQL> getPrefQLCollection() {
        return prefQLCollection;
    }

    public void setPrefQLCollection(Collection<PrefQL> prefQLCollection) {
        this.prefQLCollection = prefQLCollection;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (prefId != null ? prefId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof PrefAnnotation)) {
            return false;
        }
        PrefAnnotation other = (PrefAnnotation) object;
        if ((this.prefId == null && other.prefId != null) || (this.prefId != null && !this.prefId.equals(other.prefId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.PrefAnnotation[ prefId=" + prefId + " ]";
    }
    
}
